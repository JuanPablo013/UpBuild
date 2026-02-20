<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KnowledgeChunk;
use Illuminate\Auth\Access\HandlesAuthorization;

class KnowledgeChunkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KnowledgeChunk');
    }

    public function view(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('View:KnowledgeChunk');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KnowledgeChunk');
    }

    public function update(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('Update:KnowledgeChunk');
    }

    public function delete(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('Delete:KnowledgeChunk');
    }

    public function restore(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('Restore:KnowledgeChunk');
    }

    public function forceDelete(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('ForceDelete:KnowledgeChunk');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KnowledgeChunk');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KnowledgeChunk');
    }

    public function replicate(AuthUser $authUser, KnowledgeChunk $knowledgeChunk): bool
    {
        return $authUser->can('Replicate:KnowledgeChunk');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KnowledgeChunk');
    }

}