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
        $owner_id = auth()->id();
        $is_owner = $owner_id === $this->id;

        return [
            'id' => $this->id,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at,
            'name' => $this->name,
            'avatar_img' => $this->avatar_img,
            'total_posts' => $this->posts->where('status', 'success')->where('approval_status', 'approved')->count(),
            'total_followings' => $this->followers->count(),
            'total_followers' => $this->followings->count(),
            'is_following' => $this->followings->where('follower_id', $owner_id)->count() ? true : false,
            'is_owner' => $is_owner,
            'latest_post' => new PostCollection($this->posts?->sortByDesc('created_at')?->take(5)),
        ];
    }
}
