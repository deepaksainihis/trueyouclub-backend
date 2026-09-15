<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title top-box-set">
                        <h4 class="card-title-heading">Reported Messages</h4>
                    </div>
                    <div class="search-table-data">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 table-centered">
                            <thead>
                                <tr>
                                    <th>Room Name</th>
                                    <th>Reported By</th>
                                    <th>Reason</th>
                                    <th>Reported At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                <tr>
                                    <td>
                                        @if($report->room)
                                            {{ $report->room->name }}
                                        @else
                                            <span class="text-muted">Unknown Room</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->reporter)
                                            {{ $report->reporter->name }}
                                        @else
                                            <span class="text-muted">Unknown User</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->reason }}</td>
                                    <td>{{ $report->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <div class="update-webinar table-btns">
                                            <ul class="d-flex">
                                                @if($report->room)
                                                <li>
                                                    <a href="javascript:void(0)" onclick="openChatModal('{{ rtrim(str_replace(chr(39), '', config('constants.front_end_url')), '/') }}/community/{{ $report->room->id }}?embed=true&forceAdmin=true', '{{ $report->room->name }}')" title="Enter Room to take action">
                                                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M19.8507 7.3175C19.1191 5.7175 16.2499 0.5 9.99989 0.5C3.74989 0.5 0.880726 5.7175 0.149059 7.3175C0.0508413 7.53192 0 7.76499 0 8.00083C0 8.23668 0.0508413 8.46975 0.149059 8.68417C0.880726 10.2825 3.74989 15.5 9.99989 15.5C16.2499 15.5 19.1191 10.2825 19.8507 8.6825C19.9487 8.46832 19.9995 8.23554 19.9995 8C19.9995 7.76446 19.9487 7.53168 19.8507 7.3175ZM9.99989 13.8333C4.74406 13.8333 2.29156 9.36167 1.66656 8.00917C2.29156 6.63833 4.74406 2.16667 9.99989 2.16667C15.2432 2.16667 17.6966 6.61917 18.3332 8C17.6966 9.38083 15.2432 13.8333 9.99989 13.8333Z" fill="#626263"/>
                                                            <path d="M9.99968 3.8335C9.17559 3.8335 8.37001 4.07787 7.6848 4.53571C6.9996 4.99355 6.46554 5.64429 6.15018 6.40565C5.83481 7.16701 5.7523 8.00479 5.91307 8.81304C6.07384 9.62129 6.47068 10.3637 7.0534 10.9464C7.63612 11.5292 8.37855 11.926 9.1868 12.0868C9.99505 12.2475 10.8328 12.165 11.5942 11.8497C12.3555 11.5343 13.0063 11.0002 13.4641 10.315C13.922 9.62983 14.1663 8.82425 14.1663 8.00016C14.165 6.8955 13.7256 5.83646 12.9445 5.05535C12.1634 4.27423 11.1043 3.83482 9.99968 3.8335ZM9.99968 10.5002C9.50522 10.5002 9.02187 10.3535 8.61075 10.0788C8.19963 9.80413 7.8792 9.41369 7.68998 8.95687C7.50076 8.50006 7.45125 7.99739 7.54771 7.51244C7.64418 7.02748 7.88228 6.58203 8.23191 6.2324C8.58154 5.88276 9.027 5.64466 9.51195 5.5482C9.9969 5.45174 10.4996 5.50124 10.9564 5.69046C11.4132 5.87968 11.8036 6.20011 12.0784 6.61124C12.3531 7.02236 12.4997 7.50571 12.4997 8.00016C12.4997 8.6632 12.2363 9.29909 11.7674 9.76793C11.2986 10.2368 10.6627 10.5002 9.99968 10.5002Z" fill="#626263"/>
                                                        </svg>
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a href="javascript:void(0)" wire:click.prevent="delete({{ $report->id }})" title="Dismiss Report">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.5 4.98333C14.725 4.70833 11.9333 4.56667 9.15 4.56667C7.5 4.56667 5.85 4.65 4.2 4.81667L2.5 4.98333" stroke="#D15050" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M7.08301 4.14167L7.26634 3.05C7.39967 2.25833 7.49967 1.66667 8.90801 1.66667H11.0913C12.4997 1.66667 12.608 2.29167 12.733 3.05833L12.9163 4.14167" stroke="#D15050" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M15.7087 7.61667L15.167 16.0083C15.0753 17.3167 15.0003 18.3333 12.6753 18.3333H7.32533C5.00033 18.3333 4.92533 17.3167 4.83366 16.0083L4.29199 7.61667" stroke="#D15050" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M8.6084 13.75H11.3834" stroke="#D15050" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M7.91699 10.4167H12.0837" stroke="#D15050" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No reported messages found.</td>
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

    <!-- Chat View Modal (Same as Chat Rooms) -->
    <div class="modal fade" id="chatViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content" style="height: 85vh;">
                <div class="modal-header">
                    <h5 class="modal-title" id="chatViewModalTitle">Chat Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeChatModal()"></button>
                </div>
                <div class="modal-body p-0" style="height: calc(100% - 60px);">
                    <iframe id="chatViewIframe" src="" width="100%" height="100%" style="border: none; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openChatModal(url, roomName) {
            document.getElementById('chatViewModalTitle').innerText = roomName + ' - Chat';
            document.getElementById('chatViewIframe').src = url;
            $('#chatViewModal').modal('show');
        }
        function closeChatModal() {
            document.getElementById('chatViewIframe').src = "";
        }
    </script>
</div>
