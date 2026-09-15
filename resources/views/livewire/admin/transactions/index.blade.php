<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
    
                    @if($viewMode)
    
                       @livewire('admin.transactions.show', ['transaction_id' => $transaction_id])
    
                    @else
                        <div wire:loading wire:target="create" class="loader"></div>
                        <div class="card-title top-box-set">
                            <h4 class="card-title-heading">{{__('cruds.transaction.title')}} {{ __('global.list') }}</h4>
                        </div>
                        <div class="table-responsive search-table-data">
                          @livewire('admin.transactions.transaction-table')
                        </div>
    
                    @endif
    
                </div>
            </div>
        </div>
    </div>
    </div>
    
    
    @push('styles')
   
    @endpush
    
    @push('scripts')
   
    @endpush