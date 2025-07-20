<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo,
            'city' => $this->city,
            'phone' => $this->phone,
            'address' => $this->address,
            'about' => $this->about,
            'post_code' => $this->post_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Relasi (diload jika ada)
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'specialists' => SpecialistResource::collection($this->whenLoaded('specialists')),

            // Count-nya hanya angka
            'doctors_count' => $this->when(isset($this->doctors_count), $this->doctors_count),
            'specialists_count' => $this->when(isset($this->specialists_count), $this->specialists_count),
        ];
    }

}
