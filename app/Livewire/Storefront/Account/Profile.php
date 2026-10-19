<?php

namespace App\Livewire\Storefront\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $section = 'overview';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function setSection(string $section): void
    {
        if (in_array($section, ['overview', 'orders', 'profile', 'security'], true)) {
            $this->section = $section;
        }
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($emailChanged && method_exists($user, 'hasVerifiedEmail')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('profile-status', 'Profile updated successfully.');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        $recentOrders = Order::query()
            ->where('user_id', Auth::id())
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.storefront.account.profile', [
            'recentOrders' => $recentOrders,
        ])->layout('components.layouts.auth', [
            'title' => 'My Account | Factory',
        ]);
    }
}