<?php

namespace App\Http\Livewire\Auth\Profile;


use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use App\Models\User;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ChangePassword extends Component
{
    use LivewireAlert;

    // protected $layout = null;
    
    public $userId;
    
    public $oldPassword,$checkOldPassword;

    public $current_password, $password, $password_confirmation;

    public function mount(){
        $this->userId = auth()->user()->id;
        $this->oldPassword = auth()->user()->password;
    }

    protected function rules() 
    {
        return [
            'current_password'  => ['required', 'string','min:8',new MatchOldPassword],
            'password'   => ['required', 'string', 'min:8', /*'confirmed',*/ 'different:current_password',/*'regex:/^(?!.*\s)(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'*/],
            'password_confirmation' => ['required','min:8','same:password'],
        ];
    }

    protected function messages() 
    {
        // return getCommonValidationRuleMsgs();
        return [
            'password_confirmation.same' => 'The password confirmation and new password must match.',
            'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
        ];
    }
  
   
    public function render()
    {
        return view('livewire.auth.profile.change-password');
    }

    public function updatePassword(){
        $validated = $this->validate($this->rules(),$this->messages(), ['password' => 'new password']);
        
        User::find($this->userId)->update(['password'=> Hash::make($this->password)]);

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

    public function closeModal(){
        $this->resetInputFields();
        $this->resetValidation();
    }
    
}