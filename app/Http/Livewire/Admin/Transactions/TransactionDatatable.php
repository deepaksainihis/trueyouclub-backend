<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Models\Plan;
use App\Models\Addon;
use App\Models\Transaction;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class TransactionDatatable extends LivewireDatatable
{
    public function mount($model = false, $include = [], $exclude = [], $hide = [], $dates = [], $times = [], $searchable = [], $sort = null, $hideHeader = null, $hidePagination = null, $perPage = null, $exportable = false, $hideable = false, $beforeTableSlot = false, $buttonsSlot = false, $afterTableSlot = false, $params = [])
    {
        parent::mount($model, $include, $exclude, $hide, $dates, $times, $searchable, $sort, $hideHeader, $hidePagination, $perPage, $exportable, $hideable, $beforeTableSlot, $buttonsSlot, $afterTableSlot, $params);

        // $this->resetTable();
        $this->perPage = config('livewire-datatables.default_per_page', 10);
        $this->sort(7, 'desc');
        $this->search = null;
        $this->setPage(1);
    }

    public function builder()
    {
        $transactions = Transaction::query()
        ->leftJoin('users', 'users.id', 'transactions.user_id')
        ->leftJoin('plans', 'plans.id', 'transactions.plan_id')
        ->leftJoin('addons', 'addons.id', 'transactions.plan_id');

        return $transactions;

    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function columns()
    {
        return [
            Column::callback(['id'], function ($id) {
                return $id;
            })->sortable()->defaultSort('desc')->hide(),

            Column::index($this)->unsortable(),

            Column::callback(['users.name'], function ($name) {
                return $name;
            })->label(trans('cruds.transaction.fields.user'))->sortable()->searchable(),

            Column::callback(['plan_id','is_addon'],function($plan_id,$is_addon){
                $planTitle = '';
                if($is_addon){
                    $planTitle =  Addon::where('id',$plan_id)->value('title');
                }else{
                    $planTitle = Plan::where('id',$plan_id)->value('title');
                }
                return $planTitle;
            })->label('Plan')->sortable()->searchable(),
            
            Column::callback(['amount'], function ($amount) {
                return "<i class='fa fa-dollar'></i>".number_format($amount,2);
            })->label(trans('cruds.transaction.fields.amount'))->sortable()->searchable(),

            Column::callback(['currency'],function($currency){
                return strtoupper($currency);
            })->label(trans('cruds.transaction.fields.currency'))->sortable()->searchable(),

            // Column::callback(['payment_method'],function($payment_method){
            //     return ucwords($payment_method);
            // })->label(trans('cruds.transaction.fields.payment_method'))->sortable()->searchable(),

            Column::callback(['status'],function($status){
                return ucwords($status);
            })->label(trans('cruds.transaction.fields.status'))->sortable()->searchable(),

            DateColumn::name('created_at')->label(trans('global.created'))->format(config('constants.date_format'))->sortable()->searchable()->defaultSort('desc'),


            Column::callback(['id', 'user_id'], function ($id, $user_id) {
                $array = ['show'];
                return view('livewire.datatables.actions', ['id' => $id, 'events' => $array]);
            })->label(trans('global.action'))->unsortable(),
        ];
    }

}
