<?php

namespace App\Http\Livewire\Admin\Transactions;

use Illuminate\Support\Str;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionTable extends Component
{
    use WithPagination;

    public $search = null;
    
    public $sortColumnName = 'created_at', $sortDirection = 'desc', $perPage = 10;
    
    protected $listeners = [
        
    ];
    
    public function render()
    {
        // $statusSearch = null;
        $searchValue = $this->search;
        // if(Str::contains('active', strtolower($searchValue))){
        //     $statusSearch = 1;
        // }else if(Str::contains('inactive', strtolower($searchValue))){
        //     $statusSearch = 0;
        // }

        $transactions = Transaction::query()
        ->select('transactions.*', 'plans.title as plan_title', 'addons.title as addon_title')
        ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
        ->leftJoin('plans', function ($join) {
            $join->on('transactions.plan_id', '=', 'plans.id')
                ->where('transactions.is_addon', '=', 0);
        })
        ->leftJoin('addons', function ($join) {
            $join->on('transactions.plan_id', '=', 'addons.id')
                ->where('transactions.is_addon', '=', 1);
        })
        ->where(function ($query) use ($searchValue) {
            $query->where('users.name', 'like', '%' . $searchValue . '%')
                ->orWhere(function ($query) use ($searchValue) {
                    $query->where('plans.title', 'like', '%' . $searchValue . '%')
                        ->where('transactions.is_addon', '=', 0);
                })
                ->orWhere(function ($query) use ($searchValue) {
                    $query->where('addons.title', 'like', '%' . $searchValue . '%')
                        ->where('transactions.is_addon', '=', 1);
                })
                
                ->orWhere('transactions.amount',str_replace(',','',$searchValue))
                ->orWhere('transactions.currency','like',$searchValue.'%')
                ->orWhere('transactions.status','like',$searchValue.'%')
                ->orWhereRaw("date_format(transactions.created_at, '".config('constants.search_datetime_format')."') like ?", ['%'.$searchValue.'%']);

        });
        
        if($this->sortColumnName == 'plan-title'){
           $transactions = $transactions->orderBy('plan_title', $this->sortDirection)
           ->orderBy('addon_title', $this->sortDirection);

        }else{
            $transactions->orderBy($this->sortColumnName, $this->sortDirection);
        }

        $transactions = $transactions->paginate($this->perPage);

        return view('livewire.admin.transactions.transaction-table',compact('transactions'));
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    
    public function sortBy($columnName)
    {
        $this->resetPage();

        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = $this->swapSortDirection();
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }
    
    public function swapSortDirection()
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }
    
    
}
