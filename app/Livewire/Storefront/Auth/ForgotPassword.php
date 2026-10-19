<?php

namespace App\Livewire\Storefront\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $status = Password::sendResetLink([
            'email' => $this->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));

            $this->reset('email');

            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.storefront.auth.forgot-password')
            ->layout('components.layouts.auth', [
                'title' => 'Forgot Password',
            ]);
    }
}