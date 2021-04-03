<?php

namespace App\Nova;

use DigitalCreative\ConditionalContainer\ConditionalContainer;
use Epartment\NovaDependencyContainer\HasDependencies;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;

class Transaction extends Resource
{
    use HasDependencies;

    public static $group = 'Accounts';
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Transaction::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $a = [
            BelongsTo::make('Ledger', 'ledger_for_transaction'),
        ];
        $b = [
            BelongsTo::make('Company Ledger', 'company_ledger_for_transaction'),
        ];
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Select::make('For', 'for_transaction_type_id')
                ->options(
                    [
                        0 => 'Please Select Option',
                        1 => 'Company',
                        2 => 'Personal',
                    ]
                )->default(0),

            BelongsTo::make('Company Ledger', 'company_ledger_for_transaction')->showOnIndex(
                function () {
                    return $this->ledger_type == 1;
                }
            )->hideWhenCreating()->hideWhenUpdating(),
            BelongsTo::make('Ledger', 'ledger_for_transaction')->showOnIndex(
                function () {
                    return $this->ledger_type == 2;
                }
            )->hideWhenCreating()->hideWhenUpdating(),
            NovaDependencyContainer::make($a)->dependsOn('for_transaction_type_id', 2),
            NovaDependencyContainer::make($b)->dependsOn('for_transaction_type_id', 1),
            BelongsTo::make('Transaction Type', 'transactiontype'),
            Currency::make('Amount'),
            DateTime::make('Created At', 'created_at')->sortable()
        ];
    }


    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
