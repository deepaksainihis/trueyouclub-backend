<?php

namespace App\Http\Livewire\Admin\RewardRedemption;

use Livewire\Component;

use App\Models\RewardRedemption;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public function render()
    {
        $redemptions = RewardRedemption::with(['user', 'reward'])->latest()->get();
        return view('livewire.admin.reward-redemption.index', compact('redemptions'))
            ->extends('layouts.admin')
            ->section('content');
    }

    public function markDelivered($id)
    {
        $redemption = RewardRedemption::find($id);
        if ($redemption && $redemption->status === 'pending') {
            $redemption->status = 'delivered';
            $redemption->save();
            $this->alert('success', 'Marked as delivered!');
        }
    }
}
