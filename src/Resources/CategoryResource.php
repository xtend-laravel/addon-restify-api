<?php

namespace XtendLunar\Addons\RestifyApi\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CategoryResource extends JsonResource
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
            'id' => $this->id,
            'active' => (bool)($this->legacy_data['active'] ?? false),
            'name' => $this->attribute_data['name'] ?? null,
            'count' => DB::table('lunar_collection_product')->where('collection_id', $this->id)->count(),
            'children' => CategoryResource::collection($this->children),
        ];
    }
}
