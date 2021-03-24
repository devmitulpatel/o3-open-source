<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Query\Builder as QueryBuilder;

class ResourceIndexRequest extends NovaRequest
{
    use CountsResources, QueriesResources;

    /**
     * Get the paginator instance for the index request.
     *
     * @param ResourceIndexRequest $request
     * @param  string  $resource
     * @param  Builder  $query
     * @return array
     */
    public function searchIndex()
    {
        $resource = $this->resource();

        $perPage = $this->viaRelationship()
                        ? $resource::$perPageViaRelationship
                        : ($this->perPage ?? $resource::perPageOptions()[0]);

        return (new QueryBuilder($resource))->search(
            $this, $this->newQuery(), $this->search,
            $this->filters()->all(), $this->orderings(), $this->trashed()
        )->paginate((int) $perPage);
    }

    /**
     * Get the count of the resources.
     *
     * @return int
     */
    public function toCount()
    {
        return $this->toQuery()->toBase()->getCountForPagination();
    }
}
