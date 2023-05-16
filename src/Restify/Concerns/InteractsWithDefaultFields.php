<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Concerns;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Support\Facades\Schema;

trait InteractsWithDefaultFields
{
    public static array $excludeFields = [];

    public static array $translatableFields = [
        'attribute_data',
    ];

    public function fields(RestifyRequest $request): array
    {
        return $this->getDefaultFields(
            exclude: static::$excludeFields
        );
    }

    public function fieldsForIndex(RestifyRequest $request): array
    {
        return $this->fields($request);
    }

    public function fieldsForShow(RestifyRequest $request): array
    {
        return $this->fields($request);
    }

    public function fieldsForUpdate(RestifyRequest $request): array
    {
        return $this->fields($request);
    }

    public function fieldsForStore(RestifyRequest $request): array
    {
        return $this->fields($request);
    }

    protected function getDefaultFields(array $exclude = []): array
    {
        $fields = ! $this->model()->exists
            ? collect(Schema::getColumnListing($this->model()->getTable()))->flatMap(fn ($field) => [$field => null])->toArray()
            : $this->model()->toArray();

        return collect($fields)
            ->filter(fn ($attribute, $field) => ! in_array($field, $exclude))
            ->mapWithKeys(function ($value, $key) {
                $callback = in_array($key, static::$translatableFields)
                    ? fn () => $this->translateFields()
                    : null;
                $field = field($key, $callback);
                if (collect($this->model()->getCasts())->keys()->contains($key)) {
                    $field = $field->readOnly();
                }

                return [$key => $field];
            })->toArray();
    }

    protected function translateFields(): array
    {
        // @todo auto detect translation fields
        return [
            'name' => $this->model()->translateAttribute('name'),
            'description' => $this->model()->translateAttribute('description'),
        ];
    }
}
