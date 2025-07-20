<?php

namespace App\Http\Resources\User;

use App\Http\Resources\CreatorRequest\CreatorRequestResource;
use App\Http\Resources\Post\PostCollection;
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
            'email' => $this->email,
            'user_identify' => $this->user_identify,
            'name' => $this->name,
            'phone' => $this->phone,
            'avatar_img' => $this->avatar_img,
            'phone_verified_at' => $this->phone_verified_at,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
