<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

     protected function mutateFormDataBeforeSave(array $data): array
    {
        $isAdmin = Filament::getCurrentPanel()?->getId() === 'admin';

        if (! $isAdmin) {
            $vendor = Auth::user()?->vendor;

            if (
                ! $vendor ||
                (int) $this->record->vendor_id !== (int) $vendor->id
            ) {
                throw ValidationException::withMessages([
                    'name' => 'You are not allowed to edit this product.',
                ]);
            }

            if (
                $vendor->status !== 'active' ||
                $vendor->verification_status !== 'approved'
            ) {
                throw ValidationException::withMessages([
                    'name' => 'Your vendor account is not active and approved.',
                ]);
            }

            // Do not allow vendor to change ownership or review fields.
            $data['vendor_id'] = $vendor->id;
            $data['status'] = 'inactive';
            $data['approval_status'] = 'pending';
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
            $data['rejection_reason'] = null;

            return $data;
        }

        // Admin retains existing ownership if no vendor was submitted.
        $data['vendor_id'] = $data['vendor_id'] ?? $this->record->vendor_id;

        $newApproval = $data['approval_status'] ?? 'pending';
        $oldApproval = $this->record->approval_status;

        if ($newApproval === 'pending') {
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
            $data['rejection_reason'] = null;
        } elseif (
            $newApproval !== $oldApproval ||
            empty($this->record->reviewed_at)
        ) {
            $data['reviewed_by'] = Auth::id();
            $data['reviewed_at'] = now();

            if ($newApproval === 'approved') {
                $data['rejection_reason'] = null;
            }
        } else {
            // Preserve the previous reviewer and review timestamp.
            $data['reviewed_by'] = $this->record->reviewed_by;
            $data['reviewed_at'] = $this->record->reviewed_at;
        }

        if (
            $newApproval === 'approved' &&
            ($data['status'] ?? 'inactive') === 'active'
        ) {
            // Keep active status only for approved products.
            $data['status'] = 'active';
        } elseif ($newApproval !== 'approved') {
            $data['status'] = 'inactive';
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
