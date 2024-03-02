<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Lunar\Facades\DB;
use Lunar\Models\Language;
use Symfony\Component\HttpFoundation\Response;
use Lunar\Models\Product;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Illuminate\Contracts\Database\Query\Builder;

class ProductSearchGetter extends Getter
{
    public static $uriKey = 'search';

    public function handle(RestifyRequest $request, ?Model $model = null): JsonResponse|Response|array
    {
        $keyword = $request->get('keyword');

        if (!$keyword) {
            return response()->json([]);
        }

        $locale = config('app.locale');
        $language = Language::where('code', $locale)->first();
        $styleCollections = CollectionGroup::where('handle', 'styles')->first()?->collections ?? [];

        $products = Product::query()
            ->where('status', 'published')
            ->where(DB::raw('LOWER(attribute_data->>"$.name.value.' . $locale . '")'), 'like', '%' . strtolower($keyword) . '%')
            ->with([
                'collections' => fn(Builder $query) => $query->whereIn($query->qualifyColumn('id'), $styleCollections->pluck('id')),
                'urls'        => fn(Builder $query) => $query->where('language_id', $language->id),
                'thumbnail',
                'primaryCategory.urls'
            ])
            ->limit(12)
            ->get();

        return response()->json($products->map(function (Product $product) {
            return [
                'id'            => $product->id,
                'name'          => $product->attr('name'),
                'slug'          => $product->urls->first()?->slug,
                'category_slug' => $product->primaryCategory->urls->first()?->slug,
                'sku'           => $product->sku,
                'image'         => $product->thumbnail->getUrl('small'),
                'price'         => $product->basePrice->price->value,
                'style'         => $product->collections->first()?->attr('name'),
            ];
        }));
    }
}