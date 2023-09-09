<?php

namespace XtendLunar\Addons\RestifyApi\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\Collection;
use Lunar\Models\Url;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class CategoryResource
 * @mixin \Lunar\Models\Collection
 */
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
            'sub_heading' => $this->resource->translateAttribute('sub_heading') ?? null,
            'description' => $this->resource->translateAttribute('description') ?? null,
            'count' => $this->getCount($request),
            'children' => CategoryResource::collection(
                resource: $this->children->filter(fn (Collection $category) => $category->legacy_data['active'] ?? true),
            ),
            'thumbnail' => $this->getFirstMediaUrl('images', 'medium') ?? null,
            'gallery' => $this->getMedia('images')->map(fn (Media $media) => $media->getFullUrl()),
            'slug' => $this->resource->urls->first(function (Url $url) {
                $matchesLocale = $url->language->code === app()->getLocale();

                return $matchesLocale || $url->language->code === config('app.fallback_locale');
            })?->slug,
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
