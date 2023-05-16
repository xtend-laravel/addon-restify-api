<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Filters\Product;

use Binaryk\LaravelRestify\Filters\MatchFilter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class NewestFilter extends MatchFilter
{
    public ?string $column = 'newestOnly';

    public function filter(RestifyRequest $request, Relation|Builder $query, $value)
    {
        if ($value) {
            $query->where('created_at', '>=', now()->subDays(30))->latest();
        }

        return $query;
    }
}
