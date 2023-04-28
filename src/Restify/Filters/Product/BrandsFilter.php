<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class BrandsFilter extends MatchFilter
{
    public ?string $column = 'brand_id';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        $brandIds = explode(',', $value);

        $query->whereIntegerInRaw('brand_id', $brandIds);

        $query->orderByRaw('FIELD(brand_id, ' . implode(',', $brandIds) . ') DESC');

        return $query;
    }
}
