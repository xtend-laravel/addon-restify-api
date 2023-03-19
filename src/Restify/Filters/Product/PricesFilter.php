<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PricesFilter extends MatchFilter
{
    public ?string $column = 'price_range';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        [$min, $max] = explode(',', $value);
        return $query->whereHas('basePrice', function (Builder $query) use ($min, $max) {
            $query->whereBetween('price', [$min, $max]);
        });
    }
}
