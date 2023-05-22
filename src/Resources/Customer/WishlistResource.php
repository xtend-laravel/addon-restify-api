<?php

namespace XtendLunar\Addons\RestifyApi\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        return [
            'message' => 'Wishlist',
        ];
    }
}
