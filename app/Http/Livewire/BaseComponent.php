<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BaseComponent extends Component
{
    public $profileImageUrlUpdated = false;
    public $isConfirmed = false,$isLoader = false;
    protected $listeners = ['updateProfileImage'];
    
    public function updateProfileImage($flag){
        // Update header component with the updated image URL
        $this->profileImageUrlUpdated = $flag;
    }
}
