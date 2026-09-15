@if ($paginator->hasPages())
<div class="max-w-screen bg-white pagination-wrapper  rounded-b-lg  border-4 border-t-0 border-b-0  border-transparent ">
  <div class="items-center justify-between sm:flex">
      <div class="flex justify-start text-gray-600">
          Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} entries
      </div>

      <div class="my-4 sm:my-0">
        <div class="lg:hidden">
          <div class="flex justify-between">
              <!-- Previous Page Link -->
              @if ($paginator->onFirstPage())
              <div class="w-32 flex justify-between items-center relative px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-400 bg-gray-50">
                  <x-icons.arrow-left />
                  {{ __('Previous')}}
              </div>
              @else
              <a href="{{ $paginator->previousPageUrl() }}" id="pagination-mobile-page-previous" class="w-32 flex justify-between items-center relative px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                  <x-icons.arrow-left />
                  {{ __('Previous')}}
              </a>
              @endif


              <!-- Next Page pnk -->
              @if ($paginator->hasMorePages())
              <a href="{{ $paginator->nextPageUrl() }}" id="pagination-mobile-page-next" class="w-32 flex justify-between items-center relative items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                  {{ __('Next')}}
                  <x-icons.arrow-right />
              </a>
              @else
              <div class="w-32 flex justify-between items-center relative px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-400 bg-gray-50">
                  {{ __('Next')}}
                  <x-icons.arrow-right class="inline" />
              </div>
              @endif
              </div>
          </div>

          <div class="justify-center hidden lg:flex">
          <div class="flex overflow-hidden border border-gray-300 divide-x divide-gray-300 rounded pagination">
              <!-- Previous Page Link -->
              @if ($paginator->onFirstPage())
              <button class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 text-gray-500 bg-white"
                  disabled>
                  <span>&laquo;</span>
              </button>
              @else
              <button wire:click="previousPage"
                  id="pagination-desktop-page-previous"
                  class="relative inline-flex items-center px-2 py-2 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out bg-white hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500">
                  <span>&laquo;</span>
              </button>
              @endif

              <div class="divide-x divide-gray-300">
                  @foreach ($elements as $element)
                  @if (is_string($element))
                  <button class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium leading-5 text-gray-700 bg-white" disabled>
                      <span>{{ $element }}</span>
                  </button>
                  @endif

                  <!-- Array Of Links -->

                  @if (is_array($element))
                  @foreach ($element as $page => $url)
                  <button wire:click="gotoPage({{ $page }})"
                          id="pagination-desktop-page-{{ $page }}"
                          class="-mx-1 relative inline-flex items-center px-4 py-2 text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 {{ $page === $paginator->currentPage() ? 'bg-gray-200' : 'bg-white' }}">
                      {{ $page }}
                      </button>
                  @endforeach
                  @endif
                  @endforeach
              </div>

              <!-- Next Page Link -->
              @if ($paginator->hasMorePages())
              <button wire:click="nextPage"
                  id="pagination-desktop-page-next"
                  class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out bg-red hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500">
                  <span>&raquo;</span>
              </button>
              @else
              <button
                  class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium leading-5 text-gray-500 bg-white "
                  disabled><span>&raquo;</span></button>
              @endif
          </div>
  
          </div>

        </div>
    </div>
</div>
@endif
