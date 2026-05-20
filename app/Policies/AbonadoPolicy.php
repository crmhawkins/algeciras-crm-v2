<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Abonado;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbonadoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Abonado');
    }

    public function view(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('View:Abonado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Abonado');
    }

    public function update(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('Update:Abonado');
    }

    public function delete(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('Delete:Abonado');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Abonado');
    }

    public function restore(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('Restore:Abonado');
    }

    public function forceDelete(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('ForceDelete:Abonado');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Abonado');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Abonado');
    }

    public function replicate(AuthUser $authUser, Abonado $abonado): bool
    {
        return $authUser->can('Replicate:Abonado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Abonado');
    }

}