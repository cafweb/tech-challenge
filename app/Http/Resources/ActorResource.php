<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActorResource extends JsonResource
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
            "id"=>$this->id,
            "name"=>$this->name,
            "bio"=>$this->bio,
            "favourite_genre"=>$this->favourite_genre,
            "movies_by_genre"=>$this->movies_by_genre,
            "born_at"=>$this->getAttributes()['born_at'],
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }
}
