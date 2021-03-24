<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActionController extends Controller
{
    /**
     * List the actions for the given resource.
     *
     * @param NovaRequest $request
     * @return Response
     */
    public function index(NovaRequest $request)
    {
        $resource = $request->newResourceWith(
            ($request->resourceId
                ? $request->findModelQuery()->first()
                : null) ?? $request->model()
        );

        return response()->json([
            'actions' => $resource->availableActions($request),
            'pivotActions' => [
                'name' => $request->pivotName(),
                'actions' => $resource->availablePivotActions($request),
            ],
        ]);
    }

    /**
     * Perform an action on the specified resources.
     *
     * @param ActionRequest $request
     * @return Response
     */
    public function store(ActionRequest $request)
    {
        $request->validateFields();

        return $request->action()->handleRequest($request);
    }
}
