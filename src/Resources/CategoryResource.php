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
        return [
            'id' => $this->id,
            'active' => (bool) ($this->legacy_data['active'] ?? true),
            'name' => $this->resource->translateAttribute('name') ?? null,
            'count' => $this->getCount($request),
            'children' => CategoryResource::collection(
                resource: $this->children->filter(fn (Collection $category) => $category->legacy_data['active'] ?? true),
            ),
            'thumbnail_src' => $this->resource->thumbnail?->getFullUrl(),
        ];
    }

    protected function getCount(Request $request): int
    {
        $countQuery = $this->publishedProducts()->where('stock', '>', 0);

        match ($request->route()->getName()) {
            'new-products' => $countQuery = $countQuery->where('lunar_products.created_at', '>=', now()->subDays(90))->latest(),
            'sales' => $countQuery = $countQuery->where('legacy_data->reduction_amount', '>', 0),
            default => '',
        };

        if ($request->repositoryUri === 'brands') {
            $countQuery = $countQuery->where('brand_id', $request->repositoryId);
        }

        return $countQuery->count();
    }
}
