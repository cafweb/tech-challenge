<?php

namespace App\Http\Resources;

use App\Models\Movie;
use Illuminate\Http\Resources\Json\JsonResource;

class AppearanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            $this->mergeWhen($this->actor !== null, [
                'actor' => [
                    'id' => $this->actor_id,
                    'name' => $this->actor->name ?? null,
                ]
            ]),
            'movie' => [
                'id' => $this->movie_id,
                'name' => $this->when($this->resource instanceof Movie, $this->name ?? null, $this->movie->name ?? null),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
