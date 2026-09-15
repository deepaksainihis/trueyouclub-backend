<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">Rewards Store</h4>
                        <div class="card-top-box-item">
                            <button wire:click="create" class="btn joinBtn btn-sm btn-icon-text btn-header">
                                + {{__('global.add')}} New Reward
                            </button>
                        </div>
                    </div>
                    <div class="search-table-data">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 table-centered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Image</th>
                                    <th>Points Cost</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rewards as $reward)
                                <tr>
                                    <td>{{ $reward->title }}</td>
                                    <td>
                                        @if($reward->image_path)
                                            <img src="{{ asset('storage/' . $reward->image_path) }}" alt="{{ $reward->title }}" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td>{{ $reward->points_cost }}</td>
                                    <td>{{ $reward->stock }}</td>
                                    <td>
                                        <div class="update-webinar table-btns">
                                            <ul class="d-flex">
                                                <li>
                                                    <a href="javascript:void(0)" wire:click="edit({{ $reward->id }})">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"></path>
                                                            <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"></path>
                                                            <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0)" wire:click="delete({{ $reward->id }})">
                                                        <svg width="18" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.3337 3.33333H13.167V1.66667C13.167 1.22464 12.9914 0.800716 12.6788 0.488155C12.3663 0.175595 11.9424 0 11.5003 0L6.50033 0C6.0583 0 5.63437 0.175595 5.32181 0.488155C5.00925 0.800716 4.83366 1.22464 4.83366 1.66667V3.33333H0.666992V5H2.33366V17.5C2.33366 18.163 2.59705 18.7989 3.06589 19.2678C3.53473 19.7366 4.17062 20 4.83366 20H13.167C13.83 20 14.4659 19.7366 14.9348 19.2678C15.4036 18.7989 15.667 18.163 15.667 17.5V5H17.3337V3.33333ZM6.50033 1.66667H11.5003V3.33333H6.50033V1.66667ZM14.0003 17.5C14.0003 17.721 13.9125 17.933 13.7562 18.0893C13.6 18.2455 13.388 18.3333 13.167 18.3333H4.83366C4.61265 18.3333 4.40068 18.2455 4.2444 18.0893C4.08812 17.933 4.00033 17.721 4.00033 17.5V5H14.0003V17.5Z" fill="#626263"></path>
                                                            <path d="M8.16667 8.3335H6.5V15.0002H8.16667V8.3335Z" fill="#626263"></path>
                                                            <path d="M11.5007 8.3335H9.83398V15.0002H11.5007V8.3335Z" fill="#626263"></path>
                                                        </svg>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No rewards found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="rewardModal" tabindex="-1" role="dialog" aria-labelledby="rewardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rewardModalLabel">{{ $modalType }} Reward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="cancel"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Points Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" wire:model="points_cost">
                            @error('points_cost') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Stock <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" wire:model="stock">
                            @error('stock') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea class="form-control" wire:model="description" rows="3"></textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" class="form-control" wire:model="new_image">
                            @error('new_image') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('openModal', event => {
            $('#rewardModal').modal('show');
        });
        window.addEventListener('refreshTable', event => {
            $('#rewardModal').modal('hide');
        });
    </script>
</div>
