<?php

namespace Laravel\Nova\Http\Requests;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Fluent;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionModelCollection;
use Laravel\Nova\Fields\ActionFields;

class ActionRequest extends NovaRequest
{
    use QueriesResources;

    /**
     * Get the selected models for the action in chunks.
     *
     * @param int $count
     * @param Closure $callback
     * @return mixed
     */
    public function chunks($count, Closure $callback)
    {
        $output = [];

        $this->toSelectedResourceQuery()->when(
            !$this->forAllMatchingResources(),
            function ($query) {
                $query->whereKey(explode(',', $this->resources))
                    ->latest($this->model()->getQualifiedKeyName());
            }
        )->chunk(
            $count,
            function ($chunk) use ($callback, &$output) {
                $output[] = $callback($this->mapChunk($chunk));
            }
        );

        return $output;
    }

    /**
     * Get the query for the models that were selected by the user.
     *
     * @return Builder
     */
    protected function toSelectedResourceQuery()
    {
        if ($this->forAllMatchingResources()) {
            return $this->toQuery();
        }

        return $this->viaRelationship()
            ? $this->modelsViaRelationship()
            : tap(
                $this->newQueryWithoutScopes(),
                function ($query) {
                    $resource = $this->resource();

                    $resource::indexQuery(
                        $this,
                        $query->with($resource::$with)
                    );
                }
            );
    }

    /**
     * Determine if the request is for all matching resources.
     *
     * @return bool
     */
    public function forAllMatchingResources()
    {
        return $this->resources === 'all';
    }

    /**
     * Get the query for the related models that were selected by the user.
     *
     * @return Builder
     */
    protected function modelsViaRelationship()
    {
        return $this->findParentModel()->{$this->viaRelationship}()
            ->withoutGlobalScopes()
            ->whereIn($this->model()->getQualifiedKeyName(), explode(',', $this->resources));
    }

    /**
     * Map the chunk of models into an appropriate state.
     *
     * @param Collection $chunk
     * @return Collection
     */
    protected function mapChunk($chunk)
    {
        return ActionModelCollection::make(
            $this->isPivotAction()
                ? $chunk->map->pivot
                : $chunk
        );
    }

    /**
     * Validqte the given fields.
     *
     * @return void
     */
    public function validateFields()
    {
        $fields = collect($this->action()->fields());

        $this->validate(
            $fields->mapWithKeys(
                function ($field) {
                    return $field->getCreationRules($this);
                }
            )->all(),
            [],
            $fields->reject(
                function ($field) {
                    return empty($field->name);
                }
            )
                ->mapWithKeys(
                    function ($field) {
                        return [$field->attribute => $field->name];
                    }
                )->all()
        );
    }

    /**
     * Get the action instance specified by the request.
     *
     * @return Action
     */
    public function action()
    {
        return once(
            function () {
                return $this->availableActions()->first(
                    function ($action) {
                        return $action->uriKey() == $this->query('action');
                    }
                ) ?: abort($this->actionExists() ? 403 : 404);
            }
        );
    }

    /**
     * Get the possible actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableActions()
    {
        return $this->resolveActions()->filter->authorizedToSee($this)->values();
    }

    /**
     * Get the all actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function resolveActions()
    {
        return $this->isPivotAction()
            ? $this->newResource()->resolvePivotActions($this)
            : $this->newResource()->resolveActions($this);
    }

    /**
     * Determine if the action being executed is a pivot action.
     *
     * @return bool
     */
    public function isPivotAction()
    {
        return $this->pivotAction === 'true';
    }

    /**
     * Determine if the specified action exists at all.
     *
     * @return bool
     */
    protected function actionExists()
    {
        return $this->resolveActions()->contains(
            function ($action) {
                return $action->uriKey() == $this->query('action');
            }
        );
    }

    /**
     * Resolve the fields for database storage using the request.
     *
     * @return array
     */
    public function resolveFieldsForStorage()
    {
        return collect($this->resolveFields()->getAttributes())->map(
            function ($attribute) {
                return $attribute instanceof UploadedFile ? $attribute->hashName() : $attribute;
            }
        )->all();
    }

    /**
     * Resolve the fields using the request.
     *
     * @return ActionFields
     */
    public function resolveFields()
    {
        return once(
            function () {
                $fields = new Fluent;

                $results = collect($this->action()->fields())->mapWithKeys(
                    function ($field) use ($fields) {
                        return [$field->attribute => $field->fillForAction($this, $fields)];
                    }
                );

                return new ActionFields(
                    collect($fields->getAttributes()), $results->filter(
                    function ($field) {
                        return is_callable($field);
                    }
                )
                );
            }
        );
    }

    /**
     * Get the key of model that lists the action on its dashboard.
     *
     * When running pivot actions, this is the key of the owning model.
     *
     * @param Model
     * @return int
     */
    public function actionableKey($model)
    {
        return $this->isPivotAction()
            ? $model->{$this->pivotRelation()->getForeignPivotKeyName()}
            : $model->getKey();
    }

    /**
     * Get the many-to-many relationship for a pivot action.
     *
     * @return Relation
     */
    public function pivotRelation()
    {
        if ($this->isPivotAction()) {
            return $this->newViaResource()->model()->{$this->viaRelationship}();
        }
    }

    /**
     * Get the model instance that lists the action on its dashboard.
     *
     * When running pivot actions, this is the owning model.
     *
     * @return Model
     */
    public function actionableModel()
    {
        return $this->isPivotAction()
            ? $this->newViaResource()->model()
            : $this->model();
    }

    /**
     * Get the key of model that is the target of the action.
     *
     * When running pivot actions, this is the key of the target model.
     *
     * @param Model
     * @return int
     */
    public function targetKey($model)
    {
        return $this->isPivotAction()
            ? $model->{$this->pivotRelation()->getRelatedPivotKeyName()}
            : $model->getKey();
    }

    /**
     * Get an instance of the target model of the action.
     *
     * @return Model
     */
    public function targetModel()
    {
        return $this->isPivotAction() ? $this->pivotRelation()->newPivot() : $this->model();
    }
}
