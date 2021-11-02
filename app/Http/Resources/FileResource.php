<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'name' => $this->original_name,
            'size' => (int)$this->size,
            'mime_type' => $this->mime_type,
            'mime_subtype' => $this->mime_subtype,
            'url' => $this->url,
        ];
    }
}
