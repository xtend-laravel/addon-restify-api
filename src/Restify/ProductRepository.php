<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Fields\BelongsTo;
use Binaryk\LaravelRestify\Fields\BelongsToMany;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Models\Price;
use Xtend\Extensions\Lunar\Core\Models\Product;
use Xtend\Extensions\Lunar\Core\Models\ProductVariant;
use XtendLunar\Addons\RestifyApi\Restify\Actions\GetProductVariant;
use XtendLunar\Addons\RestifyApi\Restify\Actions\NotifyWhenAvailable;
use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductPresenter;

class ProductRepository extends Repository
{
    public static string $model = Product::class;

    public static array $excludeFields = [];

    public static string $presenter = ProductPresenter::class;

    public static bool|array $public = true;

    protected static function booting(): void
    {
        $repositoryId = request()->route()->parameter('repositoryId');
        if (is_string($repositoryId)) {
            if ($repository = ProductVariant::query()->firstWhere('sku', strtolower($repositoryId))) {
                request()->route()->setParameter('repositoryId', $repository->product_id);
            }
        }
    }

    public static function indexQuery(RestifyRequest $request, Builder|Relation $query)
    {
        return $query
            ->where('status', 'published')
            //->where('stock', '>', 0)
            ->latest();
    }

    public static function sorts(): array
    {
        return [
            'default' => function (RestifyRequest $request, Builder $query) {
                $query->reorder();
            },
            'new' => function (RestifyRequest $request, Builder $query) {
                $query->latest();
            },
            'name' => function (RestifyRequest $request, Builder $query, $direction) {
                $query->orderBy('attribute_data->name', $direction);
            },
            'price' => function (RestifyRequest $request, Builder $query, $direction) {
                $query->orderBy(
                    Price::select('price')
                        ->whereColumn('id', $query->getModel()->getTable().'.price_default_id')
                        ->limit(1), $direction);
            },
        ];
    }

    public static function matches(): array
    {
        return [
            'newest' => Filters\Product\NewestFilter::make(),
            'sale' => Filters\Product\SaleFilter::make(),
            'brands' => Filters\Product\BrandsFilter::make(),
            'categories' => Filters\Product\CategoriesFilter::make(),
            'keyword' => Filters\Product\KeywordFilter::make(),
            'prices' => Filters\Product\PricesFilter::make(),
            'colors' => Filters\Product\ColorsFilter::make(),
            'sizes' => Filters\Product\SizesFilter::make(),
        ];
    }

    public static function related(): array
    {
        return [
            BelongsToMany::make('collections', CategoryRepository::class),
            BelongsTo::make('primaryCategory', CategoryRepository::class),
        ];
    }

    public function getters(RestifyRequest $request): array
    {
        return [
            Lunar\ProductImagesGetter::new()->onlyOnShow(),
            Lunar\ProductPricesGetter::new()->onlyOnShow(),
            Lunar\ProductVariantsGetter::new()->onlyOnShow(),
            Lunar\ProductFeaturesGetter::new()->onlyOnShow(),
            Lunar\SitemapGetter::new()->onlyOnIndex(),
            //Lunar\ProductRelatedItemsGetter::new()->onlyOnShow(),
            Lunar\ProductSearchGetter::new()->onlyOnIndex(),
        ];
    }

    public function actions(RestifyRequest $request): array
    {
        return [
            GetProductVariant::make()->onlyOnShow(),
            NotifyWhenAvailable::make()->onlyOnShow(),
        ];
    }
}
