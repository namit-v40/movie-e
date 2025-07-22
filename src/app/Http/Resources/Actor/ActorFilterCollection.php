<?php

namespace App\Http\Resources\Actor;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ActorFilterCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'items' => ActorDetailResource::collection($this->collection),
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
        ];
    }
}
