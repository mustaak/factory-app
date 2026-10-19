<?php

namespace App\Livewire\Storefront\Account;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SetPassword extends Component
{
    public string $password = '';
    public string $password_confirmation = '';

    public function save()
    {
        $validated = $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => $validated['password'],
            'needs_password' => false,
        ]);

        $orderNumber = session()->pull('last_order_number');

        return $this->redirect(
            $orderNumber
                ? route('checkout.success', ['order' => $orderNumber])
                : route('account.profile'),
            navigate: false
        );
    }

    public function render()
    {
        return view('livewire.storefront.account.set-password', [
            'orderNumber' => session('last_order_number'),
        ])->layout('components.layouts.auth', [
            'title' => 'Set your password | Factory',
        ]);
    }
}