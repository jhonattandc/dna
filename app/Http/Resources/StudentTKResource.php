<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentTKResource extends JsonResource
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
            'first_name' => $this->Primer_nombre,
            'last_name' => $this->Primer_apellido,
            'email' => $this->Email,
            'password' =>  "50Je@" . substr($this->Primer_nombre,0,1) . substr($this->Numero_identificacion,0,2)
        ];
    }
}
