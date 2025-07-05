<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_identify' => $this->user_identify,
            'title' => $this->title,
            'bio' => $this->bio,
            'dob' => $this->dob,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'name' => $this->name,
            'requested_role' => $this->requested_role,
            'social_links' => $this->social_links,
            'avatar_img' => $this->avatar_img,
            'cover_img' => $this->cover_img,
            'followings_count' => $this->followings_count,
            'followers_count' => $this->followers_count,
        ];
    }
}
