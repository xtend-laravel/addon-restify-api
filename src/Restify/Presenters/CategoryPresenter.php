<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Presenters;

use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;

class CategoryPresenter extends PresenterResource implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        $collection = $this->repository->model();
        $imageSize = str_contains($collection->thumbnail?->getUrl(), 'fra1.digitaloceanspaces') ? 'medium' : '';

        return [
            'id' => $this->data['id'],
            'name' => $this->data['attribute_data']['name'],
            'description' => $this->data['attribute_data']['description'] ?? null,
            'filters' => $this->getter($request, 'filter-groups'),
            'image_url' => $collection->thumbnail?->getUrl($imageSize) ?? null,
        ];
    }
}
