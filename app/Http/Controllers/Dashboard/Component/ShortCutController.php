<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Http\Controllers\Controller;
    use App\Models\ShopProduct;
    use App\Models\ShopProductFavorites;
    use Illuminate\Http\Request;

    class ShortCutController extends Controller
    {

        public function __construct(Request $request)
        {
            parent::__construct();
        }

        public function index(Request $request)
        {
            $_response = [];
            $_shortcut = config('os_shortcut');
            if (count($_shortcut)) {
                foreach ($_shortcut as $_entity => $_data) {
                    $_items = ($_data['model'])::orderBy($_data['primary'])
                        ->get([
                            $_data['primary'],
                            'title'
                        ]);
                    switch ($_entity) {
                        case 'form':
                            $_response['form']['entity'] = 'Форма';
                            $_response['form_button']['entity'] = 'Кнопка вызова формы';
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['form']['items'][$_item->{$_data['primary']}] = $_item->title;
                                    $_response['form_button']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        case 'banner':
                            $_response['banner']['entity'] = 'Баннер';
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['banner']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        case 'block':
                            $_response['block']['entity'] = 'Блок';
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['block']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        case 'advantage':
                            $_response['advantage']['entity'] = 'Преимущество';
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['advantage']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        case 'slider':
                            $_response['slider']['entity'] = 'Слайд-шоу';
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['slider']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        case 'products':
                            $_response['products']['entity'] = 'Товары';
                            $_response['products']['multiple'] = $_data['multiple'];
                            $_items = ($_data['model'])::from('shop_products as p')
                                ->join('shop_products as m', 'm.id', '=', 'p.modify')
                                ->get([
                                    'm.id',
                                    'm.title'
                                ]);
                            if ($_items->isNotEmpty()) {
                                $_items->each(function ($_item) use (&$_response, $_data) {
                                    $_response['products']['items'][$_item->{$_data['primary']}] = $_item->title;
                                });
                            }
                            break;
                        default:

                            break;
                    }
                }
            }

            return response()
                ->json($_response, 200);
        }

    }
