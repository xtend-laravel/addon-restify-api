<?php

namespace XtendLunar\Addons\RestifyApi\Restify;

use Binaryk\LaravelRestify\Fields\FieldCollection;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithDefaultFields;
use XtendLunar\Addons\RestifyApi\Restify\Concerns\InteractsWithPresenter;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Binaryk\LaravelRestify\Repositories\Repository as RestifyRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class Repository extends RestifyRepository
{
    use InteractsWithDefaultFields;
    use InteractsWithPresenter;

    public static int $defaultPerPage = 12;

    public static int $defaultRelatablePerPage = 20;

    public static $useScout = false;

    public static function usesScout(): bool
    {
        // Note lunar has this search trait on their Searchable trait we will need to modify this condition later (for now we will check for static::$useScout)
        return static::$useScout && in_array("Laravel\Scout\Searchable", class_uses_recursive(static::newModel()));
    }

    /**
     * Build a "show" and "index" query for the given repository.
     *
     * @param  RestifyRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function mainQuery(RestifyRequest $request, Builder|Relation $query)
    {
        return $query;
    }

    /**
     * Build an "index" query for the given repository.
     *
     * @param  RestifyRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(RestifyRequest $request, Builder|Relation $query)
    {
        return $query;
    }

    /**
     * Build a "show" query for the given repository.
     *
     * @param  RestifyRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function showQuery(RestifyRequest $request, Builder|Relation $query)
    {
        return $query;
    }

    public function collectFields(RestifyRequest $request): FieldCollection
    {
        if (auth()->check() && $request->user()->customers()->exists()) {
            $request->merge([
                'customer_id' => $request->user()->customers()->first()->id,
                'user_id' => $request->user()->id,
            ]);
        }

        return parent::collectFields($request);
    }
}
