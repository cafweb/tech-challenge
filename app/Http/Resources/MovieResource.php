<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
            "year"=>$this->year,
            "synopsis"=>$this->synopsis,
            "runtime"=>$this->runtime,
            "released_at"=>$this->getAttributes()['released_at'],
            "cost"=>$this->cost,
            "genre"=>new Genre($this->genre),
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
        ];
    }
}
