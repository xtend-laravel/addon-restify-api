<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use Illuminate\Support\Str;
use Lunar\Models\Url;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class CollectionPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        return [
            'id' => $this->data['id'],
            'name' => $this->data['attribute_data']['name'],
            'slug' => $this->data['id'].'-'.Str::slug($this->data['attribute_data']['name']),
            'description' => $this->data['attribute_data']['description'] ?? null,
            'images' => $this->getter($request, 'collection-image'),
        ];
    }
}
