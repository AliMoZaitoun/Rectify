<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Enums\MediaType;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $path = $this->path;
        $type = $this->type instanceof MediaType ? $this->type->value : $this->type;

        return [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'file_name'     => basename($path),
            'original_name' => $this->original_name,
            'mediable_id'   => $this->mediable_id,
            'mediable_type' => $this->mediable_type,

            'url'           => Storage::disk('s3')->url($this->path),

            'path'          => $path,
            'type'          => $type,
            'extension'     => pathinfo($path, PATHINFO_EXTENSION),
        ];
    }
}
