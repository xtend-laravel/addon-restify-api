<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;
use XtendLunar\Addons\RestifyApi\Restify\Presenters\ProductPresenter;
use Binaryk\LaravelRestify\Fields\BelongsTo;
use Binaryk\LaravelRestify\Fields\BelongsToMany;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lunar\Models\Price;
use Xtend\Extensions\Lunar\Core\Models\Product;

class ProductRepository extends Repository
{
    public static string $model = Product::class;

    public static array $excludeFields = [];

    public static string $presenter = ProductPresenter::class;

    public static bool|array $public = true;

    public static function indexQuery(RestifyRequest $request, Builder|Relation $query)
    {
        return $query->where('status', 'published');
    }

    public static function sorts(): array
    {
        return [
            'default' => function(RestifyRequest $request, Builder $query, $direction) {
                $query->reorder();
            },
            'name' => function(RestifyRequest $request, Builder $query, $direction) {
                $query->orderBy('attribute_data->name', $direction);
            },
            'price' => function(RestifyRequest $request, Builder $query, $direction) {
                $query->orderBy(
                    Price::select('price')
                        ->whereColumn('id', $query->getModel()->getTable() . '.price_default_id')
                        ->limit(1), $direction);
            },
        ];
    }

    public static function matches(): array
    {
        return [
            'brands' => Filters\Product\BrandsFilter::make(),
            'categories' => Filters\Product\CategoriesFilter::make(),
            'keyword' => Filters\Product\KeywordFilter::make(),
            'prices' => Filters\Product\PricesFilter::make(),
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
        ];
    }
}
