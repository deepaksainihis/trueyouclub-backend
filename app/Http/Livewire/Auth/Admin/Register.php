<?php

namespace App\Http\Livewire\Auth\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Register extends Component
{
    use LivewireAlert;

    public $first_name, $last_name, $email, $phone ,$dob, $gender, $password ,$password_confirmation;

    public $referral_id, $referral_name, $address;

    public $showResetBtn = false;

    protected function rules()
    {
        return [
            'first_name' => ['required', 'string','regex:/^[^\d\s]+(\s{0,1}[^\d\s]+)*$/', 'max:255'],
            'last_name' => ['required', 'string','regex:/^[^\d\s]+(\s{0,1}[^\d\s]+)*$/', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique((new User)->getTable(), 'email')],
            'phone' => ['required'],
            'dob'   => ['required'],
            'gender'   => ['required'],
            'referral_id'   => ['required'],
            'referral_name'   => ['required'],
            'address'   => ['required'],
            // 'password' => ['required', 'string', 'min:8'],
            // 'password_confirmation' => 'min:8|same:password',
        ];
    }

    protected function messages() 
    {
        // return getCommonValidationRuleMsgs();
        return [
            'first_name.regex' => trans('validation.not_allowed_numeric'),
            'last_name.regex' => trans('validation.not_allowed_numeric'),
        ];
    }

    public function render()
    {
        return view('livewire.auth.admin.register');
    }

    public function storeRegister()
    {
        $validated = $this->validate($this->rules(),$this->messages());
 
        $data = [ 
            'first_name' => $this->first_name, 
            'last_name'  => $this->last_name, 
            'email'      => $this->email,
            'password'   => Hash::make($this->password)
        ];
        $user = User::create($data);
        if($user){
            // Assign user Role
            $user->roles()->sync([3]);

            //Verification mail sent
            $user->sendEmailVerificationNotification();

            $this->resetInputFields();

            // Set Flash Message
            $this->flash('success', trans('panel.message.check_email_verification'));
            
            return redirect()->route('auth.login');
        }else{
            $this->resetInputFields();

            // Set Flash Message
            $this->alert('error', trans('panel.message.error'));
    
        }
    
    }
    
    public function checkEmail()
    {
        $validated = $this->validate([
            'email'    => ['required','email'],
        ]);

        $user = User::where('email', $this->email)->first();
        if ($user) {
            if(is_null($user->email_verified_at)){
                $this->showResetBtn = true;
            }
            $this->addError('email', trans('panel.message.email_already_taken'));
        }else{
            $this->resetErrorBag('email');
        }
    }
}
