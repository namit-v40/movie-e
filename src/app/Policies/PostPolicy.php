<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the user can create the post.
     */
    public function create(User $user): bool
    {
        return $user->role === User::ROLE_CREATOR;
    }

    /**
     * Determine if the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $post->creator_id === $user->id;
    }

    /**
     * Determine if the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $post->creator_id === $user->id;
    }
}
