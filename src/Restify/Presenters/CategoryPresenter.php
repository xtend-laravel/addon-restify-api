<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class CategoryPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data['id'],
            'name' => $this->data['attribute_data']['name'],
            'description' => $this->data['attribute_data']['description'] ?? null,
            'filters' => !$request->has('related') ? $this->getter($request, 'filter-groups') : null,
        ];
    }
}


