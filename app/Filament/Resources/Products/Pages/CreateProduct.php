<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Vendor;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        if (! $isAdmin) {
            $vendor = Auth::user()?->vendor;

            if (
                ! $vendor ||
                $vendor->status !== 'active' ||
                $vendor->verification_status !== 'approved'
            ) {
                throw ValidationException::withMessages([
                    'name' => 'An active, approved vendor account is required.',
                ]);
            }

            $data['vendor_id'] = $vendor->id;

            // Vendor-created products must wait for admin review.
            $data['status'] = 'inactive';
            $data['approval_status'] = 'pending';
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
            $data['rejection_reason'] = null;

            return $data;
        }

        if (empty($data['vendor_id'])) {
            throw ValidationException::withMessages([
                'vendor_id' => 'Please select a vendor.',
            ]);
        }

        $data['reviewed_by'] = null;
        $data['reviewed_at'] = null;

        if (($data['approval_status'] ?? 'pending') === 'pending') {
            $data['rejection_reason'] = null;
        } elseif (($data['approval_status'] ?? null) === 'approved') {
            $data['reviewed_by'] = Auth::id();
            $data['reviewed_at'] = now();
            $data['rejection_reason'] = null;
        } elseif (($data['approval_status'] ?? null) === 'rejected') {
            $data['reviewed_by'] = Auth::id();
            $data['reviewed_at'] = now();
        }

        return $data;
    }
}
