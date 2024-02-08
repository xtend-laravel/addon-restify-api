<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Collection;
use Lunar\Models\Language;
use Lunar\Models\Product;
use Lunar\Models\Url;
use Symfony\Component\HttpFoundation\Response;

class SitemapGetter extends Getter
{
    public static $uriKey = 'sitemap';

    public function handle(RestifyRequest $request, Collection $model = null): JsonResponse|Response
    {
        $urls = $this->prepareUrls();
        $languages = Language::all()->keyBy('code');

        return $this->prepareResponse($urls, $languages);
    }

    protected function prepareUrls(): \Illuminate\Support\Collection
    {
        return Url::all()
            ->map(fn (Url $url) => [
                'id' => $url->id,
                'slug' => $url->slug,
                'element' => $url->element,
                'language_code' => $url->language->code,
                'element_type' => $url->element_type,
                'image' => $url->element_type === Product::class ? $this->getProductPrimaryImage($url->element) : null,
                'updated_at' => $url->updated_at,
            ])
            ->groupBy(['language_code', 'element_type']);
    }

    protected function prepareResponse($urls, $languages): JsonResponse
    {
        return response()->json([
            'urls' => $languages->mapWithKeys(function (Language $language) use ($urls) {
                return [
                    $language->code => $this->prepareLanguageUrls($urls, $language)
                ];
            }),
        ]);
    }

    protected function prepareLanguageUrls($urls, $language): \Illuminate\Support\Collection
    {
        return $urls->get($language->code)->mapWithKeys(function ($urls, $elementType) {
            $key = $this->getElementTypeKey($elementType);

            return [
                $key => $this->prepareElementTypeUrls($urls)
            ];
        });
    }

    protected function getElementTypeKey($elementType)
    {
        return match ($elementType) {
            Product::class => 'products',
            Collection::class => 'collections',
        };
    }

    protected function prepareElementTypeUrls($urls)
    {
        return collect($urls)
            ->map(function ($url) {
                /** @var Product|Collection $element */
                $element = $url['element'];
                return $element instanceof Product ? [
                    'slug' => $url['slug'],
                    'category_slug' => Url::query()->firstWhere([
                        'element_id' => $element->primary_category_id,
                        'element_type' => Collection::class,
                    ])->slug,
                    'image' => $url['image'],
                    'updated_at' => $url['updated_at'],
                ] : [
                    'slug' => $element->id.'-'.$url['slug'],
                    'updated_at' => $url['updated_at'],
                ];
            })
            ->unique('slug')
            ->values();
    }

    protected function getProductPrimaryImage(Product $product)
    {
        return $product->thumbnail->getUrl();
    }
}
