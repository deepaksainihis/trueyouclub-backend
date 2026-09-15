  
  <div wire:ignore.self class="modal fade inviteModal" id="InviteModal" tabindex="-1" aria-labelledby="InviteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="InviteModalLabel">Invite Volunteer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click="closeModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body pt-4">
              <form wire:submit.prevent="submitSendInvite">
                  <div class="form-group">
                      <label for="event_id">Select Event</label>
                      <select class="form-control" id="event_id" wire:model="event_id">
                          <option value="" selected >Select Event</option>
                          @foreach($events as $event)
                              <option value="{{ $event->id }}">{{ $event->title }}</option>
                          @endforeach
                      </select>
                      @error('event_id') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>
                  <div class="form-group">
                      <label for="custom_message">Custom Message</label>
                      <textarea class="form-control" id="custom_message" wire:model="custom_message" rows="3"></textarea>
                      @error('custom_message') <span class="text-danger">{{ $message }}</span> @enderror
                  </div>
                  <div class="text-center">
                      <button type="submit" class="btn btn-primary">Send Invite</button>
                  </div>
            </form>
          </div>
        </div>
      </div>
  </div>
    

