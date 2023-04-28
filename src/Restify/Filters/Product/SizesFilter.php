<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class SizesFilter extends MatchFilter
{
    public ?string $column = 'availableSizeIds';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        $optionValueIds = explode(',', $value);

        return $query->whereHas('variants', function (Builder $query) use ($optionValueIds) {
            $query->whereHas('values', function (Builder $query) use ($optionValueIds) {
                $query->whereIn('value_id', $optionValueIds);
            });
        });
    }
}
