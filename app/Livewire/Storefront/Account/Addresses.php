<?php

namespace App\Livewire\Storefront\Account;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Addresses extends Component
{
    public ?int $editingId = null;
    public bool $showForm = false;
    

    public string $label = '';
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = 'IN';
    public bool $is_default = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    protected function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'digits:10'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:6'],
            'country' => ['required', 'size:2'],
            'is_default' => ['boolean'],
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $address = $this->ownedAddress($id);

        $this->editingId = $address->id;

        $this->label = $address->label ?? '';
        $this->name = $address->name;
        $this->phone = $address->phone;
        $this->address_line1 = $address->address_line1;
        $this->address_line2 = $address->address_line2 ?? '';
        $this->city = $address->city;
        $this->state = $address->state;
        $this->postal_code = $address->postal_code;
        $this->country = $address->country;
        $this->is_default = (bool) $address->is_default;

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['address_line2'] = $data['address_line2'] ?: null;
        $data['label'] = $data['label'] ?: null;
        $data['country'] = strtoupper($data['country']);

        $userId = Auth::id();

        if ($data['is_default']) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }

        if ($this->editingId) {
            $this->ownedAddress($this->editingId)->update($data);
        } else {
            $data['user_id'] = $userId;
            // Pehla address automatically default ho jaye
            if (! Address::where('user_id', $userId)->exists()) {
                $data['is_default'] = true;
            }
            Address::create($data);
        }

        $this->resetForm();
        session()->flash('address-status', 'Address saved.');
    }

    public function setDefault(int $id): void
    {
        $address = $this->ownedAddress($id);

        Address::where('user_id', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }

    public function delete(int $id): void
    {
        $this->ownedAddress($id)->delete();
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function ownedAddress(int $id): Address
    {
        // user_id check: dusre user ka address edit/delete na ho sake
        return Address::where('user_id', Auth::id())->findOrFail($id);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'showForm', 'label', 'name', 'phone', 'address_line1',
            'address_line2', 'city', 'state', 'postal_code', 'is_default',
        ]);
        $this->country = 'IN';
    }

    public function render()
    {
        return view('livewire.storefront.account.addresses', [
            'addresses' => Address::where('user_id', Auth::id())->get(),
        ])->layout('components.layouts.auth', [
            'title' => 'My Addresses | Factory',
        ]);
    }
}