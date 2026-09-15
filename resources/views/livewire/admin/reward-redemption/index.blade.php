<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">Reward Redemptions</h4>
                    </div>
                    <div class="search-table-data">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 table-centered">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Reward</th>
                                    <th>Shipping Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($redemptions as $redemption)
                                <tr>
                                    <td>
                                        {{ optional($redemption->user)->name ?? 'Unknown User' }}<br>
                                        <small>{{ optional($redemption->user)->email }}</small>
                                    </td>
                                    <td>
                                        {{ optional($redemption->reward)->title ?? 'Deleted Reward' }}<br>
                                        <span class="badge bg-soft-primary">{{ optional($redemption->reward)->points_cost }} Points</span>
                                    </td>
                                    <td>{{ $redemption->shipping_address ?? 'N/A' }}</td>
                                    <td>
                                        @if($redemption->status == 'pending')
                                            <span class="badge bg-soft-warning">Pending</span>
                                        @else
                                            <span class="badge bg-soft-success">Delivered</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($redemption->status == 'pending')
                                            <button wire:click="markDelivered({{ $redemption->id }})" class="btn btn-sm btn-success">
                                                Mark Delivered
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No redemptions found.</td>
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
</div>
