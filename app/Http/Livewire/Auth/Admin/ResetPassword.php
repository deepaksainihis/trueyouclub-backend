<?php

namespace App\Http\Livewire\Auth\Admin;

use Illuminate\Support\Facades\DB; 
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ResetPassword extends Component
{
    use LivewireAlert;

    // protected $layout = null;

    public $token;
    public $email;
    public $password ,$password_confirmation;

    protected function rules()
    {
        return [
            'password' => ['required', 'string', 'min:8', /*'confirmed'*/],
            'password_confirmation' => ['required', 'string', 'min:8','same:password'],
        ];
    }

    protected function messages()
    {
        // return getCommonValidationRuleMsgs();
        return [
            'password.required' => 'Password is required!',
            'password.min' => 'The Password should be at least 8 letter.',
            'password.confirmed' => 'The Password should be confirm.',
            'password_confirmation.required' => 'Confirm password is required!',
            'password_confirmation.min' => 'The confirm password should be at least 8 letter.',
            'password_confirmation.same' => 'The password confirmation and password must match.',
        ];
    }

    public function mount(){
        $this->email = decrypt($this->email);
    }   

    public function render()
    {
        return view('livewire.auth.admin.reset-password');
    }

    public function submit(){
        $email_id = $this->email;
        
        $validated = $this->validate($this->rules(), $this->messages());

        $updatePassword = DB::table('password_resets')->where(['email' => $email_id,'token' => $this->token])->first();

        if(!$updatePassword){

            $this->alert('error', trans('passwords.token'));
           
        }else{
            $user = User::where('email', $email_id)
            ->update(['password' => Hash::make($this->password)]);

            DB::table('password_resets')->where(['email'=> $email_id])->delete();

            // Set Flash Message
            $this->flash('success', trans('passwords.reset'));

        }

        $this->resetInputFields();

        return redirect()->route('auth.login');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    private function resetInputFields(){
        $this->password = '';
        $this->password_confirmation = '';
    }
}
