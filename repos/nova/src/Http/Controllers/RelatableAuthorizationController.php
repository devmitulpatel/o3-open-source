<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class RelatableAuthorizationController extends Controller
{
    /**
     * Get the relatable authorization status for the resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function show(NovaRequest $request)
    {
        $model = $request->findParentModelOrFail();

        $resource = $request->viaResource();

        if (in_array($request->relationshipType, ['belongsToMany', 'morphToMany'])) {
            return ['authorized' => (new $resource($model))->authorizedToAttachAny(
                $request, $request->model()
            )];
        }

        return ['authorized' => (new $resource($model))->authorizedToAdd(
            $request, $request->model()
        )];
    }
}
