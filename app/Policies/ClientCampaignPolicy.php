<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ClientCampaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientCampaignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClientCampaign');
    }

    public function view(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('View:ClientCampaign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClientCampaign');
    }

    public function update(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('Update:ClientCampaign');
    }

    public function delete(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('Delete:ClientCampaign');
    }

    public function restore(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('Restore:ClientCampaign');
    }

    public function forceDelete(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('ForceDelete:ClientCampaign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClientCampaign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClientCampaign');
    }

    public function replicate(AuthUser $authUser, ClientCampaign $clientCampaign): bool
    {
        return $authUser->can('Replicate:ClientCampaign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClientCampaign');
    }

}