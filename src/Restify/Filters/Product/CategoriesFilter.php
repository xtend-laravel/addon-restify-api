<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Xtend\Extensions\Lunar\Core\Models\CollectionProduct;

class CategoriesFilter extends MatchFilter
{
    public ?string $column = 'collection_id';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        $categoryIds = explode(',', $value);
        $query->whereHas('categoryCollection', function (Builder $query) use ($categoryIds) {
            $query->whereIntegerInRaw($query->qualifyColumn('id'), $categoryIds);
        });

        $query->orderByDesc(
            CollectionProduct::select('collection_id')
                ->whereColumn('product_id', $query->getModel()->getTable() . '.id')
                ->whereIn('collection_id', $categoryIds)
                ->limit(1)
        );
        return $query;
    }
}
