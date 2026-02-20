<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KnowledgeDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class KnowledgeDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KnowledgeDocument');
    }

    public function view(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('View:KnowledgeDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KnowledgeDocument');
    }

    public function update(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('Update:KnowledgeDocument');
    }

    public function delete(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('Delete:KnowledgeDocument');
    }

    public function restore(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('Restore:KnowledgeDocument');
    }

    public function forceDelete(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('ForceDelete:KnowledgeDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KnowledgeDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KnowledgeDocument');
    }

    public function replicate(AuthUser $authUser, KnowledgeDocument $knowledgeDocument): bool
    {
        return $authUser->can('Replicate:KnowledgeDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KnowledgeDocument');
    }

}