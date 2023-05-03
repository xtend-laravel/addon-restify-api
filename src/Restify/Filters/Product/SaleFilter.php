<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class SaleFilter extends MatchFilter
{
    public ?string $column = 'saleOnly';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        if ($value) {
            $query->where('legacy_data->reduction_amount', '>', 0);
        }
        return $query;
    }
}
