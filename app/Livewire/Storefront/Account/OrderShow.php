<?php

namespace App\Livewire\Storefront\Account;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrderShow extends Component
{
    public string $orderNumber;

    public function mount(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    #[Computed]
    public function order(): Order
    {
        return Order::query()
            ->where('order_number', $this->orderNumber)
            ->where('user_id', Auth::id())
            ->with('items.variant.product.primaryImage')
            ->firstOrFail();
    }

    public function cancelOrder(): void
    {
        $this->resetErrorBag('cancel');

        $cancelled = DB::transaction(function () {
            $order = Order::whereKey($this->order->id)->lockForUpdate()->firstOrFail();

            if (! in_array($order->status, ['pending', 'processing'], true)) {
                return false;
            }

            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    ProductVariant::whereKey($item->product_variant_id)
                        ->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return true;
        });

        if (! $cancelled) {
            $this->addError('cancel', 'Ye order ab cancel nahi ho sakta.');
        }

        unset($this->order);
    }

    public function render()
    {
        return view('livewire.storefront.account.order-show', [
            'order' => $this->order,
        ])->layout('components.layouts.auth', [
            'title' => 'Order ' . $this->orderNumber . ' | Factory',
        ]);
    }
}