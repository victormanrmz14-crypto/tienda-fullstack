<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'slug'        => $this->slug,
            'descripcion' => $this->descripcion,
            'productos'   => ProductoResource::collection(
                                $this->whenLoaded('productos')
                             ),
        ];
    }
}