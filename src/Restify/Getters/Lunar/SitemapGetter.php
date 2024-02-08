<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Collection;
use Lunar\Models\Product;
use Lunar\Models\Url;
use Symfony\Component\HttpFoundation\Response;

class SitemapGetter extends Getter
{
    public static $uriKey = 'sitemap';

    public function handle(RestifyRequest $request, Collection $model = null): JsonResponse|Response
    {
        $urls = Url::all()
            ->map(fn (Url $url) => [
                'id' => $url->id,
                'slug' => $url->slug,
                'language_code' => $url->language->code,
                'element_type' => $url->element_type,
                'images' => $url->element_type === Product::class ? $this->getProductImages($url->element) : null,
                'updated_at' => $url->updated_at,
            ])
            ->groupBy('element_type');

        return response()->json([
            'collections' => $urls->get(Collection::class),
            'products' => $urls->get(Product::class),
            'pages' => [],
        ]);
    }

    protected function getProductImages(Product $product)
    {
        return $product->images->map(fn ($image) => $image->getUrl('small'));
    }
}
