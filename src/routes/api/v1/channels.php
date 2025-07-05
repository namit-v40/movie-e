<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-user.{userId}', function ($user, $userId) {
    return $user->id == $userId;
});
Broadcast::channel('admin.notifications', function ($user) {
    return $user->role === 'admin';
});
