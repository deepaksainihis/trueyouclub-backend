<?php

namespace App\Http\Livewire\Auth\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;


class Index extends Component
{
    use LivewireAlert,WithFileUploads;

    protected $layout = null;

    public $editMode =false, $showConfirmCancel = false;

    public $authUser, $profile_image = null;

    public $first_name, $last_name, $email,$phone; 

    public $current_password, $password, $password_confirmation;

    protected $listeners = [
        'confirmUpdateProfileImage','cancelUpdateProfileImage','openEditSection','closedEditSection'
    ];

    public function mount(){
        $this->authUser = auth()->user();
        $this->first_name = $this->authUser->first_name;
        $this->last_name  = $this->authUser->last_name;
        $this->email      = $this->authUser->email;
        $this->phone      = $this->authUser->phone;
    }

    public function render()
    {
        return view('livewire.auth.profile.index');
    }

    public function validateProfileImage()
    {
        $this->showConfirmCancel = true;

        $this->validate([
            'profile_image' => 'image|max:1024', // Maximum file size of 1MB
        ]);
       
    }

    public function confirmUpdateProfileImage()
    {
        $this->showConfirmCancel = false;
        $this->validate([
            'profile_image' => 'image|max:1024', // Maximum file size of 1MB
        ]);
        $user = $this->authUser;

        $actionType = 'save';
        $uploadId = null;
        if($profileImageRecord = $user->profileImage){
            $uploadId = $profileImageRecord->id;
            $actionType = 'update';
        }
        $response = uploadImage($user, $this->profile_image, 'user/profile-images',"profile", 'original', $actionType, $uploadId);

        
        $this->reset(['profile_image']);

        $this->authUser->profile_image_url = $response->file_url;

        $profileUrl = auth()->user()->profile_image_url;
        $this->render();
        if ($response) {
        
            $this->dispatchBrowserEvent('refreshProfileImages',['imgUrl'=>$profileUrl]);
        
            // Set Flash Message
            $this->alert('success', 'Profile image has been updated.');


        } else {
            $this->alert(trans('panel.alert-type.error'), trans('panel.message.error'));
        }
    }

    public function cancelUpdateProfileImage()
    {
        $this->showConfirmCancel = false;

        $this->reset(['profile_image']);
    }


    public function openEditSection(){
        $this->editMode = true;
      
        $this->first_name = $this->authUser->first_name;
        $this->last_name  = $this->authUser->last_name;
        $this->email      = $this->authUser->email;
        $this->phone      = $this->authUser->phone;
    }

    public function closedEditSection(){
        $this->editMode = false;
        $this->resetFields();
    }

    public function updateProfile(){
    //    dd($this->all());

        $userId = auth()->user()->id;

        $validatedDate = $this->validate([
            'first_name'  => 'required',
            'last_name'   => 'required',
            'phone' => [
                'required',
                'integer',
                'regex:/^[0-9]{7,15}$/',
                'not_in:-',
                Rule::unique('users', 'phone')->ignore($userId, 'id')
            ],
        ],[
            'phone.required'=>'The phone number field is required',
            'phone.regex' =>'The phone number length must be 7 to 15 digits.',
            'phone.unique' =>'The phone number already exists.',
        ]);

        $userDetails = [];
        $userDetails['first_name'] = $this->first_name;
        $userDetails['last_name']  = $this->last_name;
        $userDetails['name']       = $this->first_name.' '.$this->last_name;
        $userDetails['phone']      = $this->phone;

        $this->authUser->update($userDetails);

        $fullName = $userDetails['name'];
        
        $this->resetInputFields();

        $this->dispatchBrowserEvent('refreshDocument',['authUserName'=>$fullName]);

        // $this->closedEditSection();
        $this->alert('success', 'Profile has been updated.');
    }

    public function resetFields(){
        $this->first_name = '';
        $this->last_name  = '';
        $this->email      = '';
        $this->phone      = '';
    }

    
    public function updatePassword(){
        $validated = $this->validate([
            'current_password'  => ['required', 'string','min:8',new MatchOldPassword],
            'password'   => ['required', 'string', 'min:8', /*'confirmed',*/ 'different:current_password'],
            'password_confirmation' => ['required','min:8','same:password'],
        ],
        [
            'password_confirmation.same' => 'The password confirmation and new password must match.'
        ], ['password' => 'new password']);
        
        User::find($this->authUser->id)->update(['password'=> Hash::make($this->password)]);

        $this->resetInputFields();
        $this->resetInputFields();

        $this->dispatchBrowserEvent('close-modal',['element'=>'#changePasswordModal']);

        // Set Flash Message
        $this->alert('success', trans('passwords.updated'));

    }

    private function resetInputFields(){
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }
}
