<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Vendor');
    }

    public function view(AuthUser $authUser, Vendor $vendor): bool
    {
        return $authUser->can('View:Vendor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Vendor');
    }

    public function update(AuthUser $authUser, Vendor $vendor): bool
    {
        return $authUser->can('Update:Vendor');
    }

    public function delete(AuthUser $authUser, Vendor $vendor): bool
    {
        return $authUser->can('Delete:Vendor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Vendor');
    }

}