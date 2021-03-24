<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class UpdateStatusAction extends Action
{
    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param Collection $models
     * @return string|void
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $this->markAsFailed($models->where('id', 1)->first(), 'Test Message');
        $this->markAsFinished($models->where('id', 2)->first());
    }
}
