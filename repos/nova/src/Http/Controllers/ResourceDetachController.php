<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Contracts\Deletable;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Http\Requests\DetachResourceRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

class ResourceDetachController extends Controller
{
    /**
     * Detach the given resource(s).
     *
     * @param DetachResourceRequest $request
     * @return Response
     */
    public function handle(DetachResourceRequest $request)
    {
        $request->chunks(150, function ($models) use ($request) {
            $parent = $request->findParentModelOrFail();

            foreach ($models as $model) {
                $this->deletePivotFields(
                    $request, $resource = $request->newResourceWith($model),
                    $pivot = $model->{$parent->{$request->viaRelationship}()->getPivotAccessor()}
                );

                $pivot->delete();

                tap(Nova::actionEvent(), function ($actionEvent) use ($pivot, $model, $parent, $request) {
                    DB::connection($actionEvent->getConnectionName())->table('action_events')->insert(
                        $actionEvent->forResourceDetach(
                            $request->user(), $parent, collect([$model]), $pivot->getMorphClass()
                        )->map->getAttributes()->all()
                    );
                });
            }
        });
    }

    /**
     * Delete the pivot fields on the given pivot model.
     *
     * @param DetachResourceRequest $request
     * @param  Resource  $resource
     * @param  Model
     * @return void
     */
    protected function deletePivotFields(DetachResourceRequest $request, $resource, $pivot)
    {
        $resource->resolvePivotFields($request, $request->viaResource)
            ->whereInstanceOf(Deletable::class)
            ->filter->isPrunable()
            ->each(function ($field) use ($request, $pivot) {
                DeleteField::forRequest($request, $field, $pivot)->save();
            });
    }
}
