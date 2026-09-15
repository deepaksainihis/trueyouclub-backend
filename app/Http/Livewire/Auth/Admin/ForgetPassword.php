<?php

namespace App\Http\Livewire\Auth\Admin;

use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon; 
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Rules\ValidEmail;

class ForgetPassword extends Component
{
    use LivewireAlert;

    // protected $layout = null;

    public $email;

    public function render()
    {
        return view('livewire.auth.admin.forget-password');
    }

    public function submit(){
        $this->validate(['email' => ['required','email','exists:users',new ValidEmail]], getCommonValidationRuleMsgs());
        DB::beginTransaction();
        try {

            $this->email = strtolower($this->email);

            $user = User::where('email',$this->email)->whereHas('roles', function($q){ $q->where('id', 1);})->first();
           
            if($user){
                $token = Str::random(64);
                $email_id = $this->email;
                
                $userDetails = array();
                $userDetails['name'] = ucwords($user->first_name.' '.$user->last_name);

                $userDetails['reset_password_url'] = route('auth.reset-password',[$token,encrypt($email_id)]);
            
                DB::table('password_resets')->insert([
                    'email' => $email_id, 
                    'token' => $token, 
                    'created_at' => Carbon::now()
                ]);

                $subject = 'Reset Password Notification';
                Mail::to($email_id)->queue(new ResetPasswordMail($userDetails['name'],$userDetails['reset_password_url'], $subject));

                DB::commit();

                // Set Flash Message
                $this->alert('success', trans('passwords.sent'));
            }else{
                // Set Flash Message
                $this->alert('error', 'Invalid email!');
            }

        
            $this->resetInputFields();
        }
        catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            $this->alert('error',trans('messages.error_message'));
        }
    }

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    private function resetInputFields(){
        $this->email = '';
    }
}
