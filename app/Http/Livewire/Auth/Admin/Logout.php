<?php

namespace App\Http\Livewire\Auth\Admin;

use Auth;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Logout extends Component
{
    use LivewireAlert;

    protected $layout = null;

    public $type;

    protected $listeners = [
        'confirmLogout'
    ];

    public function render()
    {
        return view('livewire.auth.admin.logout');
    }

    public function logout()
    {
        $this->confirm('Are you sure you want to Logout?', [
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes',
            'cancelButtonText' => 'No',
            'onConfirmed' => 'confirmLogout',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
        ]);
        
    }

    public function confirmLogout(){
        Auth::logout();
        
        // Redirect to the login page
        return redirect()->route('auth.login');
    }
}
