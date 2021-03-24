<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

trait InteractsWithRelatedResources
{
    /**
     * Find the parent resource model instance for the request.
     *
     * @return Resource
     */
    public function findParentResourceOrFail()
    {
        return once(function () {
            $resource = $this->viaResource();

            return new $resource($this->findParentModelOrFail());
        });
    }

    /**
     * Get the class name of the "via" resource being requested.
     *
     * @return string
     */
    public function viaResource()
    {
        return Nova::resourceForKey($this->viaResource);
    }

    /**
     * Find the parent resource model instance for the request or abort.
     *
     * @return Model|null
     */
    public function findParentModelOrFail()
    {
        return $this->findParentModel() ?: abort(404);
    }

    /**
     * Find the parent resource model instance for the request.
     *
     * @return Model|null
     */
    public function findParentModel()
    {
        return once(function () {
            if (! $this->viaRelationship()) {
                return;
            }

            return Nova::modelInstanceForKey($this->viaResource)
                                ->newQueryWithoutScopes()
                                ->find($this->viaResourceId);
        });
    }

    /**
     * Determine if the request is via a relationship.
     *
     * @return bool
     */
    public function viaRelationship()
    {
        return $this->viaResource && $this->viaResourceId && $this->viaRelationship;
    }

    /**
     * Find the parent resource model instance for the request or abort.
     *
     * @return Model|null
     */
    public function findRelatedModelOrFail()
    {
        return $this->findRelatedModel() ?: abort(404);
    }

    /**
     * Find the parent resource model instance for the request.
     *
     * @return Model|null
     */
    public function findRelatedModel()
    {
        return once(function () {
            return Nova::modelInstanceForKey($this->relatedResource)
                ->newQueryWithoutScopes()
                ->find($this->input($this->relatedResource));
        });
    }

    /**
     * Get the displayable pivot model name for a "via relationship" request.
     *
     * @return string
     */
    public function pivotName()
    {
        if (! $this->viaRelationship()) {
            return Resource::DEFAULT_PIVOT_NAME;
        }

        $resource = Nova::resourceInstanceForKey($this->viaResource);

        if ($name = $resource->pivotNameForField($this, $this->viaRelationship)) {
            return $name;
        }

        return ($parent = $this->findParentModel())
                    ? class_basename($parent->{$this->viaRelationship}()->getPivotClass())
                    : Resource::DEFAULT_PIVOT_NAME;
    }

    /**
     * Get a new instance of the "via" resource being requested.
     *
     * @return Resource
     */
    public function newViaResource()
    {
        $resource = $this->viaResource();

        return new $resource($resource::newModel());
    }

    /**
     * Get a new instance of the "related" resource being requested.
     *
     * @return Resource
     */
    public function newRelatedResource()
    {
        $resource = $this->relatedResource();

        return new $resource($resource::newModel());
    }

    /**
     * Get the class name of the "related" resource being requested.
     *
     * @return string
     */
    public function relatedResource()
    {
        return Nova::resourceForKey($this->relatedResource);
    }
}
