 <!-- Show entries & Search box -->
 <div wire:loading wire:target="search" class="loader"></div>

 <div class="flex items-center justify-between mb-1">
    <div class="w-100 flex items-center">                
        <div class="w-100 items-center justify-between p-2 sm:flex">
            <div class="flex items-center my-2 sm:my-0">
                <span class="items-center justify-between p-2 sm:flex"> 
                    Show 
                    <select class="ml-2 mr-2 border block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 form-select leading-6 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5" wire:model="paginationLength">
                        @foreach(config('constants.datatable_entries') as $length)
                            <option value="{{ $length }}">{{ $length }}</option>
                        @endforeach
                    </select>
                    entries
                </span>
            </div>
            <div class="flex justify-end text-gray-600">
                <div class="flex rounded-lg w-96 shadow-sm">
                        <div class="relative flex-grow focus-within:z-10">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" stroke="currentColor" fill="none">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input wire:model.debounce.500ms="search" class="block w-full py-3 pl-10 text-sm border-gray-300 leading-4 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 focus:outline-none" placeholder="{{ isset($searchBoxPlaceholder) ? $searchBoxPlaceholder : 'Search'}}" type="text">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                <button wire:click="$set('search', null)" class="text-gray-300 hover:text-red-600 focus:outline-none">
                                    <svg class="h-5 w-5 stroke-current w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<!-- End Show entries & Search box -->