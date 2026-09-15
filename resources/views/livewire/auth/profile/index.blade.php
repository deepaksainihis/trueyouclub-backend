<div class="content-wrapper">
    <div wire:loading wire:target="openEditSection,closedEditSection" class="loader" role="status" aria-hidden="true"></div>
    <!-- <div class="card mb-4">
        <div class="card-body">
            <div class="top-box-set">
                <h4>My Profile</h4>
            </div>
        </div>
    </div> -->
    <div class="row profile-page ">
        <div class="col-12 card">
            <div class="row card-body">
                <div class="col-12 col-lg-5 col-md-5 col-xl-4">
                    <div class="card mb-5">
                        <div class="card-body">           
                            <div class="card-block">
                                @include('livewire.auth.profile.profile-image')
                            </div>            
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-7 col-md-7 col-xl-8">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body-">
                                    <div class="card-title top-box-set">
                                        <h4>Profile Details</h4>
                                        <!-- <a href="javascript:void()" class="edit-wrap">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12.5003 18.9583H7.50033C2.97533 18.9583 1.04199 17.025 1.04199 12.5V7.49996C1.04199 2.97496 2.97533 1.04163 7.50033 1.04163H9.16699C9.50866 1.04163 9.79199 1.32496 9.79199 1.66663C9.79199 2.00829 9.50866 2.29163 9.16699 2.29163H7.50033C3.65866 2.29163 2.29199 3.65829 2.29199 7.49996V12.5C2.29199 16.3416 3.65866 17.7083 7.50033 17.7083H12.5003C16.342 17.7083 17.7087 16.3416 17.7087 12.5V10.8333C17.7087 10.4916 17.992 10.2083 18.3337 10.2083C18.6753 10.2083 18.9587 10.4916 18.9587 10.8333V12.5C18.9587 17.025 17.0253 18.9583 12.5003 18.9583Z" fill="#40658B"/>
                                                <path d="M7.08311 14.7417C6.57478 14.7417 6.10811 14.5584 5.76645 14.225C5.35811 13.8167 5.18311 13.225 5.27478 12.6L5.63311 10.0917C5.69978 9.60838 6.01645 8.98338 6.35811 8.64172L12.9248 2.07505C14.5831 0.416716 16.2664 0.416716 17.9248 2.07505C18.8331 2.98338 19.2414 3.90838 19.1581 4.83338C19.0831 5.58338 18.6831 6.31672 17.9248 7.06672L11.3581 13.6334C11.0164 13.975 10.3914 14.2917 9.90811 14.3584L7.39978 14.7167C7.29145 14.7417 7.18311 14.7417 7.08311 14.7417ZM13.8081 2.95838L7.24145 9.52505C7.08311 9.68338 6.89978 10.05 6.86645 10.2667L6.50811 12.775C6.47478 13.0167 6.52478 13.2167 6.64978 13.3417C6.77478 13.4667 6.97478 13.5167 7.21645 13.4834L9.72478 13.125C9.94145 13.0917 10.3164 12.9084 10.4664 12.75L17.0331 6.18338C17.5748 5.64172 17.8581 5.15838 17.8998 4.70838C17.9498 4.16672 17.6664 3.59172 17.0331 2.95005C15.6998 1.61672 14.7831 1.99172 13.8081 2.95838Z" fill="#40658B"/>
                                                <path d="M16.5413 8.19173C16.483 8.19173 16.4246 8.1834 16.3746 8.16673C14.183 7.55006 12.4413 5.8084 11.8246 3.61673C11.733 3.2834 11.9246 2.94173 12.258 2.84173C12.5913 2.75006 12.933 2.94173 13.0246 3.27506C13.5246 5.05006 14.933 6.4584 16.708 6.9584C17.0413 7.05006 17.233 7.40006 17.1413 7.7334C17.0663 8.01673 16.8163 8.19173 16.5413 8.19173Z" fill="#40658B"/>
                                            </svg>
                                        </a> -->
                                    </div> 
                                    @include('livewire.auth.profile.edit')              
                                    <div class="card-title top-box-set">
                                        <h4>Change Password</h4>
                                    </div>
                                    @include('livewire.auth.profile.change-password')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        
        document.addEventListener('refreshProfileImages', function(event) {
            $('.refresh-profile-image img').attr('src',event.detail.imgUrl);
        });

        document.addEventListener('refreshDocument', function(event) {
            $('.auth-user-name').html(event.detail.authUserName);
        });


        $(document).ready(function(){
            $(document).on('click','#changepassword',function(){
            $('#changePasswordModal').modal('show');
            });
            
            $(document).on('click','.close-modal',function(){
                $('#changePasswordModal').modal('hide');
            });


            $(document).on('click', '.toggle-password', function() {

                $(this).toggleClass("eye-open");
                
                var input = $("#currentpass_log_id");
                input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
            });
            $(document).on('click', '.toggle-password1', function() {

                $(this).toggleClass("eye-open");
                
                var input = $("#newpass_log_id");
                input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
            });
            $(document).on('click', '.toggle-password2', function() {
                $(this).toggleClass("eye-open");
                
                var input = $("#connewpass_log_id");
                input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
            });
        });
    </script>
@endpush