<?php

namespace XtendLunar\Addons\RestifyApi\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\Collection;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        $countQuery = $this->publishedProducts()->where('stock', '>', 0);
        if ($request->repositoryUri === 'brands') {
            $countQuery = $countQuery->where('brand_id', $request->repositoryId);
        }

        return [
            'id' => $this->id,
            'active' => (bool) ($this->legacy_data['active'] ?? true),
            'name' => $this->resource->translateAttribute('name') ?? null,
            'count' => $countQuery->count(),
            'children' => $request->repositoryUri !== 'brands' ? CategoryResource::collection(
                resource: $this->children->filter(fn (Collection $category) => $category->legacy_data['active'] ?? true),
            ) : null,
        ];
    }
}
