<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\CreatorRequest\CreatorRequestResource;

class UserDetailWithCreatorResource extends UserDetailResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'followings_count' => $this->followings_count,
            'followers_count' => $this->followers_count,
            'posts_count' => $this->posts_count,
        ]);
    }
}
