<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'photo' => $this->photo,
        //     'about'=> $this->about,
        //     'address' => $this->address,
        //     'post_code' => $this->post_code,
        //     'phone' => $this->phone,

        //     'doctors_count' =>
        // ]
    }
}
