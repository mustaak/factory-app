<?php

namespace App\Livewire\Storefront\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public function render()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->withCount('items')
            ->with('items.variant.product.primaryImage')
            ->latest()
            ->paginate(10);

        return view('livewire.storefront.account.orders', [
            'orders' => $orders,
        ])->layout('components.layouts.auth', [
            'title' => 'My Orders | Factory',
        ]);
    }
}