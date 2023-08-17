<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class KeywordFilter extends MatchFilter
{
    public ?string $column = 'keyword';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        $query->whereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.name")) LIKE ?', ['%'.strtolower($value).'%']);
        $query->orWhere(fn(Builder $query) => $query->whereHas('variants', function (Builder $query) use ($value) {
            return $query->where('sku', $value);
        }));

        // @todo do we need to search by description or any other fields?
        return $query;
    }
}
