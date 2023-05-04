<?php

namespace XtendLunar\Addons\RestifyApi\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(\Illuminate\Http\Request $request)
    {
        return [
            'id' => $this->id,
            'active' => (bool)($this->legacy_data['active'] ?? true),
            'name' => $this->resource->translateAttribute('name') ?? null,
            'count' => DB::table('lunar_collection_product')->where('collection_id', $this->id)->count(),
            'children' => $request->repositoryUri !== 'brands' ? CategoryResource::collection($this->children) : null,
        ];
    }
}
