<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function created(User $user) {}

    public function updated(User $user)
    {
        if ($user->wasChanged('role') && $user->role === User::ROLE_CREATOR) {
            $user->plans()->create([
                'name' => config('constants.name_plan_default'), // todo change name JP
                'price' => 0,
                'note' => null,
                'is_default' => true,
            ]);
        }
    }
}
