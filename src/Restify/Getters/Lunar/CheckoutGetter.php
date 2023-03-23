<?php

namespace XtendLunar\Addons\RestifyApi\Restify\Getters\Lunar;

use Binaryk\LaravelRestify\Getters\Getter;
use Binaryk\LaravelRestify\Http\Requests\GetterRequest;
use Illuminate\Http\JsonResponse;
use Lunar\Facades\ShippingManifest;
use Xtend\Extensions\Lunar\Core\Models\Cart;

class CheckoutGetter extends Getter
{
    public static $uriKey = 'checkout';

    public function handle(GetterRequest $request): JsonResponse
    {
        // @todo fetch cart from session
        $cart = Cart::first();

        $shippingOptions = ShippingManifest::getOptions($cart);

        return data([
            'addresses' => [
              [
                'id' => 1,
                'address_type' => 'shipping',
                'first_name' => 'Marie',
                'last_name' => 'Dupont',
                'address_line_1' => '5 Rue de la Paix',
                'address_line_2' => '',
                'city' => 'Paris',
                'state' => '',
                'zip_code' => '75001',
              ],
              [
                'id' => 2,
                'address_type' => 'billing',
                'first_name' => 'Jean',
                'last_name' => 'Lefebvre',
                'address_line_1' => '10 Rue des Lilas',
                'address_line_2' => '',
                'city' => 'Lyon',
                'state' => '',
                'zip_code' => '69005',
              ],
              [
                'id' => 3,
                'address_type' => 'shipping',
                'first_name' => 'Camille',
                'last_name' => 'Dubois',
                'address_line_1' => '25 Rue des Champs-Élysées',
                'address_line_2' => '',
                'city' => 'Nice',
                'state' => '',
                'zip_code' => '06000',
              ],
              [
                'id' => 4,
                'address_type' => 'billing',
                'first_name' => 'Luc',
                'last_name' => 'Roux',
                'address_line_1' => '50 Avenue des Ternes',
                'address_line_2' => '',
                'city' => 'Marseille',
                'state' => '',
                'zip_code' => '13008',
              ],
              [
                'id' => 5,
                'address_type' => 'shipping',
                'first_name' => 'Élise',
                'last_name' => 'Martin',
                'address_line_1' => '15 Rue de la République',
                'address_line_2' => '',
                'city' => 'Bordeaux',
                'state' => '',
                'zip_code' => '33000',
              ],
              [
                'id' => 6,
                'address_type' => 'billing',
                'first_name' => 'Théo',
                'last_name' => 'Fournier',
                'address_line_1' => '20 Rue des Rosiers',
                'address_line_2' => '',
                'city' => 'Lille',
                'state' => '',
                'zip_code' => '59000',
              ],
              [
                'id' => 7,
                'address_type' => 'shipping',
                'first_name' => 'Anaïs',
                'last_name' => 'Girard',
                'address_line_1' => '30 Rue de la Gare',
                'address_line_2' => '',
                'city' => 'Strasbourg',
                'state' => '',
                'zip_code' => '67000',
              ],
              [
                'id' => 8,
                'address_type' => 'billing',
                'first_name' => 'Alexandre',
                'last_name' => 'Berger',
                'address_line_1' => '7 Rue du Faubourg Saint-Honoré',
                'address_line_2' => '',
                'city' => 'Toulouse',
                'state' => '',
                'zip_code' => '31000',
              ],
              [
                'id' => 9,
                'address_type' => 'shipping',
                'first_name' => 'Sophie',
                'last_name' => 'Moreau',
                'address_line_1' => '2 Rue de la Liberté',
                'address_line_2' => '',
                'city' => 'Nantes',
                'state' => '',
                'zip_code' => '44000',
              ],
              [
                'id' => 10,
                'address_type' => 'billing',
                'first_name' => 'Antoine',
                'last_name' => 'Roy',
                'address_line_1' => '40 Rue de la Liberté',
                'address_line_2' => '',
                'city' => 'Toulouse',
                'state' => '',
                'zip_code' => '31002',
              ],
            ],
            'shipping_methods' => $shippingOptions,
        ]);
    }
}
