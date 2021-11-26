<?php

namespace App\Http\Controllers\Callback;

use App\Library\BaseController;
use App\Library\NovaPoshta;
use App\Models\Seo\SearchIndex;
use App\Models\Shop\Basket;
use App\Models\Shop\Category;
use App\Models\Shop\Compare;
use App\Models\Shop\Form;
use App\Models\Shop\Gift;
use App\Models\Shop\Stock;
use App\Models\Shop\Order;
use App\Models\Shop\OrderProduct;
use App\Models\Shop\Price;
use App\Models\Shop\Product;
use App\Models\Shop\Quantity;
use App\Models\Shop\StockNotice;
use App\Notifications\ShopFormNotification;
use App\Notifications\ShopOrderNotification;
use Carbon\Carbon;
use Iiko\Biz\Exception\IikoResponseException;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Kolirt\Frontpad\Facade\Frontpad;
use League\Flysystem\Exception;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ShopController extends BaseController
{

    use Authorizable;
    use Notifiable;

    public function __construct()
    {
        parent::__construct();
    }

    public function buy_one_click_form(Request $request)
    {
        try {
            $_form_id = $request->get('form_id', 0);
            if ($_form_id) {
                $_validate_message = '';
                $_validate_rules = [
                    'name'  => 'required|string',
                    'phone' => 'required|string|phoneNumber|phoneOperatorCode',
                    //                    'captcha' => 'required|reCaptchaV3',
                ];
                $_validator = Validator::make($request->all(), $_validate_rules, [], [
                    'name'  => trans('forms.fields.buy_one_click.name'),
                    'phone' => trans('forms.fields.buy_one_click.phone'),
                    //                    'captcha' => trans('forms.fields.captcha'),
                ]);
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => "#{$_form_id} *",
                        'data'   => 'uk-form-danger error'
                    ]
                ];
                if ($_validator->fails()) {
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $_validate_message .= "<div>{$_message[0]}</div>";
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($_field, $_form_id),
                                'data'   => 'uk-form-danger error'
                            ]
                        ];
                    }
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => $_validate_message
                        ]
                    ];
                } else {
                    $_product = Product::find($request->get('product_id'));
                    $_save_data = [];
                    if ($_fields = $request->get('fields')) {
                        // todo: тут сохранение, если есть дополнительные поля
                    }
                    $_save = [
                        'product_id'   => $request->get('product_id'),
                        'product_name' => $_product->getTranslation('title', $this->defaultLocale),
                        'quantity'     => $request->get('quantity', 1),
                        'price'        => $request->get('price'),
                        'data'         => json_encode($_save_data),
                        'form'         => 'buy_one_click',
                        'name'         => $request->get('name'),
                        'phone'        => $request->get('phone'),
                        'email'        => $request->get('email'),
                        'comment'      => $request->get('comment'),
                        'referer_path' => $request->headers->get('referer')
                    ];
                    $_item = new Form();
                    $_item->fill($_save);
                    $_item->save();
                    Notification::route('mail', env('MAIL_ADMIN_NOTIFICATION'))
                        ->notify(new ShopFormNotification($_item));
                    $_response['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => View::first([
                                "frontend.{$this->deviceTemplate}.partials.modal",
                                'backend.partials.modal'
                            ], [
                                'message' => trans("forms.messages.buy_one_click.thanks")
                            ])->render(function ($view, $_content) {
                                return clear_html($_content);
                            }),
                            'id'          => 'message-ajax-modal',
                            'classDialog' => 'uk-margin-auto-vertical uk-width-auto',
                            'classModal'  => 'uk-flex uk-flex-top'
                        ]
                    ];
                    $_response['message'] = trans("notifications.thanks_shop_form_{$_item->form}");
                    $_response['result'] = TRUE;
                    spy("На сайте оставлена заявка на заказ товара \"<a href='{$_product->generate_url}' class='uk-text-bold' target='_blank'>{$_item->product_name}</a>\" в количестве <span class='uk-text-bold'>{$_item->quantity}</span> ед. <a href='/oleus/shop-forms-data/{$_item->id}/edit'>Просмотреть данные отправки</a>.", 'success');
                }
            } else {
                $_form_id = 'form-buy-one-click';
                $_form_generate = form_generate([
                    'id'                => $_form_id,
                    'action'            => _r('ajax.shop_buy_one_click'),
                    'button_send_class' => 'uk-button-success',
                    'button_send_title' => trans('frontend.button.submit_message'),
                    'fields'            => [
                        field_render('product_id', [
                            'type'  => 'hidden',
                            'value' => $request->get('product')
                        ]),
                        field_render('name', [
                            'value'      => NULL,
                            'required'   => TRUE,
                            'attributes' => [
                                'autofocus'   => TRUE,
                                'placeholder' => trans('forms.fields.buy_one_click.name'),
                            ],
                            'form_id'    => $_form_id
                        ]),
                        field_render('phone', [
                            'value'      => NULL,
                            'required'   => TRUE,
                            'attributes' => [
                                'placeholder' => trans('forms.fields.buy_one_click.phone'),
                                'class'       => 'phone-mask uk-input'
                            ],
                            'class'      => '',
                            'form_id'    => $_form_id,
                        ]),
                        //                        field_render('quantity', [
                        //                            'value'      => NULL,
                        //                            'type'       => 'number',
                        //                            'attributes' => [
                        //                                'min'         => 1,
                        //                                'step'        => 1,
                        //                                'placeholder' => trans('forms.fields.buy_one_click.quantity'),
                        //                            ],
                        //                            'form_id'    => $_form_id,
                        //                        ]),
                    ],
                    'title'             => trans('frontend.button.price_list'),
                    'modal'             => TRUE
                ]);
                $_response['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content'     => $_form_generate,
                        'id'          => 'constructor-form-ajax-modal',
                        'classDialog' => 'uk-margin-auto-vertical',
                        'classModal'  => 'uk-flex uk-flex-top'
                    ]
                ];
            }
        } catch (\Exception $e) {
            report($e);
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                ]
            ];
        }

        return response($_response, 200);
    }

    public function buy(Request $request)
    {
        try {
            global $wrap;
            $_form_id = $request->get('form');
            $_type = $request->get('type');
            $_basket = Basket::init();
            $_validate_message = '';
            $_validate_rules = [
                'name'  => 'required|string',
                'phone' => 'required|string|phoneNumber|phoneOperatorCode',
                //                'captcha'                 => 'required|reCaptchaV3',
            ];
            
            if ($_type == 'full') {
                $_validate_rules = array_merge($_validate_rules, [
                    'agreement'               => 'required',
                    'delivery_address.street' => 'required_if:delivery_method,delivery',
                    'delivery_address.house'  => 'required_if:delivery_method,delivery'
                ]);
            }
            $_validator = Validator::make($request->all(), $_validate_rules, [], [
                'name'                    => trans('forms.fields.checkout.name'),
                'phone'                   => trans('forms.fields.checkout.phone'),
                //                'captcha'                 => trans('forms.fields.captcha'),
                'delivery_address.street' => trans('forms.fields.checkout.delivery_street'),
                'delivery_address.house'  => trans('forms.fields.checkout.delivery_house_full'),
                'agreement'               => trans('forms.fields.checkout.agree_2'),
            ]);
            $_response['commands'][] = [
                'command' => 'removeClass',
                'options' => [
                    'target' => "#{$_form_id} *",
                    'data'   => 'uk-form-danger'
                ]
            ];
            
            if ($_validator->fails()) {
                foreach ($_validator->errors()->messages() as $_field => $_message) {
                    $_validate_message .= "<div>{$_message[0]}</div>";
                    $_response['commands'][] = [
                        'command' => 'addClass',
                        'options' => [
                            'target' => '#' . generate_field_id($_field, $_form_id),
                            'data'   => 'uk-form-danger',
                            'pos'    => 'top-right',
                        ]
                    ];
                }
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'status' => 'danger',
                        'text'   => $_validate_message,
                        'pos'    => 'top-right',
                    ]
                ];
            } else {
                $_config = config('os_shop');
                $_amount = $_basket->total_amount;
                if ($_amount < $_config['min_amount']) {
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => trans('shop.notifications.min_amount', ['min' => $_config['min_amount']]),
                            'pos'    => 'top-right',
                        ]
                    ];

                    return response($_response, 200);
                }
               
                $_iiko_requestOrder = $_basket->getOrderRequest($request);

                
                $_check_create_order = NULL;
                try {
                    $_check_create_order = app('iiko')->OrdersApi()->checkCreate($_iiko_requestOrder);
                } catch (IikoResponseException $e) {
                    report($e);
                } catch (\Exception $e) {
                    report($e);
                }

 
                //                if (($_u = Auth::user()) && $_u->id == 1) {
                //                    dd($_check_create_order);
                //                }
                //                if ($_check_create_order['resultState'] != 0) {
                //                    $_carbon_now = Carbon::now();
                //                    $_message_iiko = [
                //                        1 => 'Сумма заказа меньше минимальной необходимой.',
                //                        2 => $_carbon_now->hour >= 22 ? 'Извините, мы уже на сегодня не принимаем заказы. Вы можете воспользоваться функцией предзаказа.' : 'Извините, мы еще на сегодня не принимаем заказы. Вы можете воспользоваться функцией предзаказа.',
                //                        3 => 'Указанный адрес доставки не был найден.',
                //                        4 => 'Продукт из заказа находятся в стоп-листе.',
                //                        5 => 'Продукт из заказа запрещен к продаже.',
                //                    ];
                //                    $_response['commands'][] = [
                //                        'command' => 'UK_notification',
                //                        'options' => [
                //                            'status' => 'danger',
                //                            'text'   => $_message_iiko[$_check_create_order['resultState']],
                //                            'pos'    => 'top-right',
                //                        ]
                //                    ];
                //
                //                    return response($_response, 200);
                //                }
                //                if (Auth::user()) {
                //                    $_request_data = $request->only([
                //                        'name',
                //                        'phone',
                //                        //                        'payment_method',
                //                        //                        'delivery_method',
                //                        //                        'delivery_address',
                //                        //                        'comment',
                //                        //                        'surrender',
                //                        //                        'person',
                //                    ]);
                //                    $_delivery_method = $_request_data['delivery_method'] ?? NULL;
                //                    $_payment_method = $_request_data['payment_method'] ?? NULL;
                //                    if ($_delivery_method && $_delivery_method == 'pickup') {
                //                        $_iiko_requestOrder['order']['discountCardTypeId'] = '1b479511-ca7f-4df8-9d64-3dd33b6c00e8';
                //                    }
                //                    if ($_payment_method && $_payment_method == 'cash') {
                //                        $_iiko_requestOrder['order']['paymentItems'] = [
                //                            [
                //                                'paymentType'           => [
                //                                    //                                'id'   => '09322f46-578a-d210-add7-eec222a08871',
                //                                    'code' => 'CASH'
                //                                ],
                //                                'sum'                   => $_basket->total_amount_without_modification,
                //                                'isProcessedExternally' => FALSE,
                //                            ]
                //                        ];
                //                    } elseif ($_payment_method) {
                //                        $_iiko_requestOrder['order']['paymentItems'] = [
                //                            [
                //                                'paymentType'           => [
                //                                    //                                'id'   => 'e46b4e6c-10d5-a739-8fb1-b6674d1e65e7',
                //                                    'code' => 'VISA'
                //                                ],
                //                                'sum'                   => $_basket->total_amount_without_modification,
                //                                'isProcessedExternally' => FALSE,
                //                            ]
                //                        ];
                //                    }
                //                    $_iiko_order = NULL;
                //                    //                    $_iiko_order = app('iiko')->OrdersApi()->addOrder($_iiko_requestOrder);
                //                    dd($_iiko_order, $_iiko_requestOrder, $_iiko_requestOrder);
                //
                //                }

                if ($_type == 'quick') {
                    $_save = $request->only([
                        'name',
                        'phone',
                        'type'
                    ]);
                    $_save['status'] = 1;
                } else {
                    $_save = $request->only([
                        'name',
                        'phone',
                        'type',
                        'payment_method',
                        'delivery_method',
                        'delivery_address',
                        'comment',
                        'surrender',
                    ]);
                    $_save['pre_order_at'] = NULL;
                    $_save['user_id'] = $request->user()->id ?? NULL;
                    $_save['status'] = $_save['payment_method'] == 'card' ? 0 : 1;
                    $_save['birthday'] = (int)($_save['birthday'] ?? 0);
                    $_save['call_me_back'] = (int)($_save['call_me_back'] ?? 1);
                    $_save['coupon'] = $request->input('certificate');
                    if ($_gift = Gift::getGift()) $_save['gift_id'] = $_gift['id'] ?? NULL;
                    $_pre_order_at = $request->input('pre_order_at');
                    if ($_pre_order_at) {
                        $_pre_order_at = Carbon::parse($_pre_order_at);
                        $_new_at = Carbon::now();
                        if ($_new_at->diffInMinutes($_pre_order_at) < 90) $_pre_order_at = $_new_at->addMinutes(90);
                        $_save['pre_order_at'] = $_pre_order_at->format('Y-m-d H:i:s');
                    }
                    if ($_save['delivery_method'] && $_save['delivery_method'] == 'pickup') {
                        $_iiko_requestOrder['order']['discountCardTypeId'] = '1b479511-ca7f-4df8-9d64-3dd33b6c00e8';
                    }
                    if ($_save['payment_method'] && $_save['payment_method'] == 'cash') {
                        $_iiko_requestOrder['order']['paymentItems'] = [  //** Элементы оплаты заказа */
                            [
                                'paymentType'           => [ //** Тип оплаты (одно из полей: id, code является обязательным) */
                                    'code' => 'CASH'
                                ],
                                'sum'                   => $_basket->total_amount_without_modification,  //** Сумма к оплате */
                                'isProcessedExternally' => FALSE,                                        //** Является ли позиция оплаты проведенной */
                            ]
                        ];
                    }
                }
                $_save['amount'] = $_basket->total_amount_without_modification;
                $_save['delivery_free'] = 1;

                if(!empty($_basket->promo_code)){
                    $_save['promo_code'] = json_encode($_basket->promo_code);
                    $_iiko_requestOrder['coupon'] = $_basket->promo_code['code'];
                }

                $_order = new Order();
                $_order->fill($_save);
                $_order->save();

                //                if (Auth::user()) {
                //                    dd($_order);
                //                }
                //                $_eCommerce = [
                //                    'transaction_id' => $_order->id,
                //                    'affiliation'    => '',
                //                    'value'          => $_order->amount,
                //                    'currency'       => 'UAH',
                //                    'tax'            => 0,
                //                    'shipping'       => 0,
                //                    'items'          => []
                //                ];
                foreach ($_basket->getFormationOrders() as $_product) {
                    $_order_product = new OrderProduct();
                    $_order_product->fill([
                        'order_id'     => $_order->id,
                        'product_sku'  => $_product['sku'],
                        'product_id'   => $_product['id'],
                        'product_name' => $_product['title'],
                        'quantity'     => $_product['quantity'],
                        'price'        => $_product['price'],
                        'amount'       => $_product['amount'],
                        'spicy'        => $_product['spicy'],
                        'certificate'  => $_product['certificate'],
                        'composition'  => $_product['composition'] ? json_encode($_product['composition']) : NULL,
                    ]);
                    $_product['product']->increment('sale_statistics');
                    $_order->_products()->save($_order_product);
                    //                    $_eCommerce['items'][] = [
                    //                        'id'       => $_product['sku'],
                    //                        'name'     => $_product['product']->getTranslation('title', 'ua'),
                    //                        'quantity' => $_product['quantity'],
                    //                        'price'    => $_product['price']
                    //                    ];
                }

                if ($_type == 'full') {
                    $_order_discount = 0;
                    if ($_save['delivery_method'] == 'pickup') {
                        $_order_discount = $_basket->getDeliveryAmount();
                    }

                    if ($_order_discount) {
                        //                        if ($_basket->certificate && $_basket->certificate['type'] == 'product' && $_save['delivery_method'] != 'pickup') $_order_discount = 0;
                        $_order->where('id', $_order->id)->update([
                            'discount'             => $_order_discount,
                            'amount_less_discount' => $_order->amount - $_order_discount
                        ]);
                    }
                    // $_order_discount = $_basket->certificate && $_basket->certificate['discount_amount'] ? $_basket->certificate['discount_amount'] : 0;
                    // if ($_save['delivery_method'] == 'pickup') {
                    //     $_discount = $_basket->getDeliveryAmount();
                    //     if ($_discount) $_order_discount += $_discount;
                    //     if ($_discount && $_basket->certificate && $_basket->certificate['type'] == 'product') $_order_discount = $_discount;
                    // $_order->delivery_free = 1;
                    // } elseif ($_order->amount > config('os_shop.delivery_free_amount')) {
                    //     $_order->delivery_free = 1;
                    // } else {
                    //     $_order->amount += config('os_shop.delivery_amount');
                    // }
                    // if ($_order_discount) {
                    //     if ($_basket->certificate && $_basket->certificate['type'] == 'product' && $_save['delivery_method'] != 'pickup') $_order_discount = 0;
                    //     $_order->discount = $_order_discount;
                    //     $_order->amount_less_discount = $_order->amount - $_order_discount;
                    // }
                }
                $_order_amount = $_order->amount_less_discount ? : $_order->amount;
                if ($_order->status == 0) {
                    //                    if (($_u = Auth::user()) && $_u->id == 1) {
                    //                        $_order_amount = 1;
                    //                    }
                    $_response['commands'][] = [
                        'command' => 'append',
                        'options' => [
                            'target' => 'body',
                            'data'   => '<div id="liqPay-form" class="uk-hidden">' . $_order->getLiqpayApiForm($_order_amount) . '</div>'
                        ]
                    ];
                    $_response['commands'][] = [
                        'command' => 'eval',
                        'options' => [
                        'data' => "$('#liqPay-form form').submit();"
                        ]
                    ];
                } else {
                    //                    $_fb = "if (typeof fbq == 'function'){var a = {}; a.locale = '{$wrap['locale']}'; a.device = '{$wrap['device']['type']}'; a.content_type = 'product'; a.content_name = 'Оформлення замовлення'; a.content_ids = '{$_basket->sku}'; a.value = " . ($_basket->amount ? $_basket->amount['original']['price'] : NULL) . "; a.currency = 'UAH'; a.num_items = {$_basket->quantity_in}; a.contents = {$_basket->product_list}; fbq('track', 'Purchase', a);}";
                    $_order_amount = view_price($_order_amount, $_order_amount);
                    spy("На сайте оставлен заказ товаров <a href='/oleus/shop-orders/{$_order->id}/edit' class='uk-text-bold' target='_blank'>№{$_order->id}</a> на сумму <span class='uk-text-bold'>{$_order_amount['format']['view_price']}</span> {$_order_amount['currency']['suffix']}. Текущий статус заказа <span class='uk-text-uppercase uk-text-bold'>" . trans('shop.status.' . $_order->status) . "</span>. <a href='/oleus/shop-orders/{$_order->id}/edit'>Просмотреть данные заказа</a>.", 'success');
                    Cookie::queue(Cookie::forget('frontPad_certificate'));
                    $_basket->bClear(); //** clear basket  */
                    Basket::promoCodeSave('promo_code','');

                    Notification::route('mail', env('MAIL_ADMIN_NOTIFICATION'))
                        ->notify(new ShopOrderNotification($_order));

                    try {

                        if (empty($_check_create_order['resultState'])) {
 
                            $_iiko_order = app('iiko')->OrdersApi()->addOrder($_iiko_requestOrder);

                            $_order->update([
                                'rk_order_id'     => $_iiko_order['orderId'],
                                'rk_order_number' => $_iiko_order['number'],
                                'rk_order_sum'    => $_iiko_order['sum']
                            ]);

                         }
                        
                    } catch (Exception $e) {
                        report($e);
                    }
                    //                    $_order->setDataToFrontpad();
                    // редирект на страницу благодарности
                    session()->put('order', $_order->id);
                    //                    session()->put('fb', $_fb);
                    //                    $_response['commands'][] = [
                    //                        'command' => 'eval',
                    //                        'options' => [
                    //                            'data' => "if (typeof gtag == 'function'){gtag('event', 'purchase', " . json_encode($_eCommerce) . ");}",
                    //                        ]
                    //                    ];
                    $_response['commands'][] = [
                        'command' => 'redirect',
                        'options' => [
                            'url'  => _r('page.shop_checkout_thanks_page'),
                            'time' => 500
                        ]
                    ];
                }

            }
        } catch (\Exception $e) {
            report($e);
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                    'pos'    => 'top-right',
                ]
            ];
        }

        return response($_response, 200);
    }

    public function search_product(Request $request)
    {
        $_query_string = trim($request->input('string'));
        $_category = trim($request->input('category', 'all'));
        $_response = '<div class="search-product">';
        $_response .= '<div class="uk-child-width-1-3@s uk-grid">';
        if ($_query_string) {
            $_search_model = new SearchIndex(new Product(), 'sku');
            $_found_result = $_search_model->query_search($_query_string, $_category, FALSE);
            $_found_result_page = $_search_model->query_search($_query_string, $_category, TRUE);
            $_found_result_total = $_found_result_page->total();
            $_count_result = count($_found_result);
            $_load_more_number = $_found_result_total - $_count_result;
            if ($_found_result->isNotEmpty()) {
                $_found_result->each(function ($_item) use (&$_response) {
                    $_mark = $_item->mark[0] ?? NULL;
                    $_response .= '<div class="item-search uk-text-center"><div class="item-search-product uk-flex uk-flex-column uk-flex-between uk-height-1-1 uk-product-is-' . $_mark . '">';
                    $_response .= '<div class="item-search-top"><div class="media"><a class="uk-display-block" href="' . $_item->generate_url . '">';
                    if ($_item->preview_fid) {
                        $_response .= $_item->_preview_asset('productTeaser_180_150', [
                            'only_way'   => FALSE,
                            'attributes' => [
                                'alt' => strip_tags($_item->title),
                            ]
                        ]);
                    } else {
                        $_response .= image_render(NULL, 'productTeaser_180_150', [
                            'no_last_modify' => FALSE,
                            'only_way'       => FALSE,
                            'attributes'     => [
                                'alt' => strip_tags($_item->title),
                            ]
                        ]);
                    }
                    $_response .= '</a></div>';
                    $_response .= '<div><div class="mark-product">';
                    if ($_mark) {
                        $_response .= trans('shop.marks.' . $_mark);
                    } else {
                    }
                    $_response .= '</div>';
                    $_response .= '<a class="uk-display-block title" href="' . $_item->generate_url . '">' . $_item->title . '</a></div></div><div>';
                    if ($_item->price['view_price']) {
                        //                        $_response .= '<div class="item-search-in-stock uk-text-uppercase">' . trans('shop.product.are_available') . '</div>';
                        $_response .= '<div class="">';
                        //                        $_response .= '<div class="price">' . $_item->price['price']['format']['view_price_2'] . '</div>';
                        $_response .= '<a href="' . $_item->generate_url . '" class="uk-button uk-button-success uk-text-uppercase">' . trans('frontend.view_availability') . '</a></div>';
                    }
                    //                    elseif ($_item->price['pharmacy_min_price_exist']) {
                    //                        $_response .= '<div class="item-search-in-stock uk-text-uppercase">' . trans('shop.product.are_available_in_pharmacy') . '</div>';
                    //                        $_response .= '<div class="uk-flex uk-flex-wrap uk-flex-between uk-flex-bottom"><div class="price">' . $_item->price['pharmacy_min_price']['format']['view_price_2'] . '</div>';
                    //                        $_response .= '<a href="' . $_item->generate_url . '" class="uk-button uk-button-success uk-text-uppercase">' . trans('forms.buttons.buy.view_availability') . '</a></div>';
                    //                    }
                    else {
                        //                        $_response .= '<div class="item-search-not-available uk-text-uppercase">' . trans('shop.product.not_available') . '</div>';
                        //                        $_response .= '<div class="uk-flex uk-flex-right"><button type="button" data-path="' . _r('ajax.shop_notify_when_appears') . '" data-product="' . $_item->id . '" class="uk-button uk-button-default uk-text-uppercase use-ajax">Сообщить о наличии</button></div>';
                    }
                    $_response .= '</div></div></div>';
                });
                if ($_found_result_total > $_count_result) {
                    $_response .= '<div class="uk-flex uk-flex-right uk-width-1-1"><a class="link-search-result uk-text-center uk-width-1-1" href="/search?query=' . $_query_string . '&category=' . $_category . '">' . trans('frontend.load_more_number', ['load_more_search' => $_load_more_number]) . '</a></div>';
                }
            } else {
                $_response .= '<div class="not-found-items">' . trans('frontend.not_found_items') . ' "' . $_query_string . '"' . '</div>';
            }
        }

        return $_response;
    }

    public function payment_box(Request $request)
    {
        $_method = $request->get('option', 'cash');
        $_form_id = 'form-checkout-order';
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-payment-method-box-form-field-box',
                'data'   => field_render('payment_method_box', [
                    'type'    => 'markup',
                    'html'    => View::first([
                        "frontend.{$this->deviceTemplate}.shops.checkout_form_payment_box",
                        "frontend.default.shops.checkout_form_payment_box",
                        'backend.base.checkout_form_payment_box'
                    ], [
                        '_payment_method' => $_method
                    ])->render(function ($view, $_content) {
                        return clear_html($_content);
                    }),
                    'form_id' => $_form_id,
                ])
            ]
        ];

        return response($_response, 200);
    }

    public function delivery_box(Request $request)
    {
        $_method = $request->get('option', 'pickup');
        $_basket = app('basket');
        $_basket->setDelivery($_method);
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount',
                'data'   => '<h6>' . trans('forms.labels.checkout.total_amount_3', [
                        'amount' => '&nbsp;&nbsp;</h6><span class="price-amount">' . $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'] . '</span>'
                    ])

            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-delivery-amount',
                'data'   => $_basket->showDeliveryString() ? : ''
            ]
        ];
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-delivery-fields-box',
                'data'   => view('frontend.default.shops.checkout_delivery_fields', [
                    'type'    => $_method,
                    'form_id' => 'form-checkout-order'
                ])->render(function ($view, $content) {
                    return clear_html($content);
                })
            ]
        ];
        $_gifts = Gift::getInfo();
        if ($_gifts) {
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#gifts-box',
                    'data'   => \view('frontend.default.shops.gifts', ['gifts' => $_gifts])
                        ->render(function ($view, $content) {
                            return clear_html($content);
                        })
                ]
            ];
        }

        return response($_response, 200);
    }

    public function basket_action(Request $request, Price $price, $action = 'add')
    {
        try {
            global $wrap;
            $_count = (int)$request->get('quantity', 1);
            $_spicy = (int)$request->get('spicy', 0);
            $_composition = $request->get('composition');
            $_type = $request->get('type', 'full');
            $_basket = app('basket');
            $_basket_action = $_basket->{$action}($price, $_count, $_spicy, $_composition);
            $_product = $price->_product;
            //            dd($_product);
            $_basket = $_basket_action === FALSE ? $_basket : $_basket_action['basket'];
            $_product->price = $_product->_render_price();
            $_items = NULL;
            //            $_template_mark = NULL;
            //            $_template_target = '#shop-product-action-box';
            //            if ($_type == 'teaser') {
            //                $_template_mark = '_teaser';
            //                $_template_target = ".shop-product-action-box-{$_product->id}";
            //            } elseif ($_type == 'compare') {
            //                $_template_mark = '_compare';
            //                $_template_target = "#shop-product-{$_product->id}-price";
            //            } elseif ($_type == 'availability') {
            //                $_template_mark = '_availability';
            //                $_template_target = "#uk-product-availability-price-box-{$_product->id}";
            //                $_items = $_product->getAvailability();
            //            }
            //            $_response['commands'][] = [
            //                'command' => 'replaceWith',
            //                'options' => [
            //                    'target' => $_template_target,
            //                    'data'   => View::first([
            //                        "frontend.{$this->deviceTemplate}.shops.product{$_template_mark}_price",
            //                        "frontend.default.shops.product{$_template_mark}_price",
            //                        "backend.base.shop_product{$_template_mark}_price"
            //                    ], [
            //                        '_item'  => $_product,
            //                        '_items' => $_items
            //                    ])->render(function ($view, $_content) {
            //                        return clear_html($_content);
            //                    }),
            //                ]
            //            ];
            //            $_template_target_product = ".product-id-{$_product->id}";
            //            $_response['commands'][] = [
            //                'command' => 'addClass',
            //                'options' => [
            //                    'target' => $_template_target_product,
            //                    'data'   => 'product-in-basket'
            //                ]
            //            ];
            if ($_basket_action === FALSE) {
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => trans('notifications.error_shop_product_max_quantity_added'),
                        'status' => 'warning',
                    ]
                ];
            } else {
                //                    $_response['commands'][] = [
                //                        'command' => 'UK_modal',
                //                        'options' => [
                //                            'content'     => '<button class="uk-modal-close-outside" type="button" uk-close></button>' . trans('notifications.thanks_shop_add_to_basket'),
                //                            'classModal'  => 'uk-flex-top',
                //                            'classDialog' => 'uk-margin-auto-vertical uk-modal-body',
                //                        ]
                //                    ];
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'status'  => 'success',
                        'text'    => trans('notifications.thanks_shop_add_to_basket_notification'),
                        'pos'     => 'top-right',
                        'timeout' => '1000'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#basket-box a span',
                        'data'   => $_basket->quantity_in,
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'addClass',
                    'options' => [
                        'target' => '#basket-box',
                        'data'   => 'shake'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#basket-box',
                        'data'   => 'uk-hidden'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'addClass',
                    'options' => [
                        'target' => '#basket-box a',
                        'data'   => 'not-empty'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#basket-box a',
                        'data'   => 'uk-disabled'
                    ]
                ];

                $_response['commands'][] = [
                    'command' => 'replaceWith',
                    'options' => [
                        'target' => '#form-checkout-order-products',
                        'data'   => $_basket->show_checkout_products($_basket)
                    ]
                ];

                /** updating prices in the basket */
                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#checkout-order-total-amount2 .price-amount',
                        'data'   => $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix']
                    ]
                ];
 
                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#checkout-order-total-amount .price-amount',
                        'data'   => (
                            !empty($_basket->total_amount_promo['format']['view_price']) ? 
                            $_basket->total_amount_promo['format']['view_price']. '&nbsp;' . $_basket->total_amount_promo['currency']['suffix']:
                            $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'])
                    ]
                ];

        
            }
            if ($action == 'add') {
                $cat = NULL;
                $_categories = $_product->_category;
                $_category_product = NULL;
                if ($_categories->isNotEmpty()) {
                    $_category_product = $_categories->first()->getTranslation('title', 'ua');
                    $_categories->each(function ($_category) use (&$cat) {
                        $cat[] = "'{$_category->title}'";
                    });
                }
                $cat = $cat ? '[' . implode(',', $cat) . ']' : NULL;
                $_price_product = $_product->price['view_price'] ? (count($_product->price['view']) > 1 ? $_product->price['view'][1]['format']['price'] : $_product->price['view'][0]['format']['price']) + $_basket_action['composition_amount'] : 0;
                $_response['commands'][] = [
                    'command' => 'eval',
                    'options' => [
                        'data' => "if (typeof fbq == 'function'){var a = {}; a.locale = '{$wrap['locale']}'; a.device = '{$wrap['device']['type']}'; a.content_name = '{$_product->getTranslation('title', 'ua')}'; a.content_type = 'product'; a.content_category = {$cat}; a.content_ids = '{$_product->sku}'; a.value = {$_price_product}; a.currency = 'UAH'; fbq('track', 'AddToCart', a);}if (typeof PO == 'object'){PO.clear();}if (typeof gtag == 'function'){gtag('event', 'add_to_cart', {items: [{id:'{$_product->sku}', name: '{$_product->getTranslation('title', 'ua')}', category: '{$_category_product}', quantity: 1, price: {$_price_product}}]});}"
                    ]
                ];
            }
        } catch (\Exception $e) {
            report($e);
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                ]
            ];
        }

        return response($_response, 200);
    }

    public function add_product_to_basket(Request $request, Product $product)
    {
        try {
            $_basket = app('basket');
            $product->price = $product->_render_price($_basket);

            if ($product->price['view_price']) {
                $_basket_action = $_basket->add($product, 1);
                $product->price = $_basket_action === FALSE ? $product->_render_price($_basket) : $product->_render_price($_basket_action);
                $_basket_template = $_basket->show_checkout_products($_basket);

                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '.form-checkout-order-products-output',
                        'data'   => clear_html($_basket_template),
                    ]
                ];

                $_response['commands'][] = [
                    'command' => 'text',
                    'options' => [
                        'target' => '#basket-box .quantity',
                        'data'   => $_basket_action->quantity_in
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'addClass',
                    'options' => [
                        'target' => '#basket-box',
                        'data'   => 'visible'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'addClass',
                    'options' => [
                        'target' => '#compare-box',
                        'data'   => 'position-top'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'eval',
                    'options' => [
                        'data' => 'console.log(1);'
                    ]
                ];
            } else {
                $_response['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content' => trans('notifications.shop_product_not_add_product_to_basket')
                    ]
                ];
            }
        } catch (\Exception $e) {
            report($e);
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                ]
            ];
        }

        return response($_response, 200);
    }

    public function recount_products(Request $request)
    {
        $_is_page = $request->get('page', 0);
        $_products = $request->get('items');
        $_method = $request->get('delivery_method', 'pickup');
        $_basket = app('basket');
        $_basket->setDelivery($_method);
        $_basket->recount($_products);
        $_gifts = Gift::getInfo();
        if ($_gifts) {
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#gifts-box',
                    'data'   => \view('frontend.default.shops.gifts', ['gifts' => $_gifts])
                        ->render(function ($view, $content) {
                            return clear_html($content);
                        })
                ]
            ];
        }
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-products',
                'data'   => $_basket->show_checkout_products($_basket, $_is_page)
            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount',
                'data'   => '<h6>' . trans('forms.labels.checkout.total_amount_3', [
                        'amount' => '&nbsp;&nbsp;</h6><span class="price-amount">' . $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'] . '</span>'
                    ])
            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-delivery-amount',
                'data'   => $_basket->showDeliveryString() ? : ''
            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#basket-box a',
                'data'   => '<img uk-img="data-src:template/images/icon-shop.svg" alt="" width="69" height="37" uk-svg><span class="icon-basket uk-flex uk-flex-middle uk-flex-center">' . $_basket->quantity_in . '</span>',
            ]
        ];

        /** updating prices in the basket  */
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount2 .price-amount',
                'data'   => $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix']
            ]
        ];

        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount .price-amount',
                'data'   => (
                    !empty($_basket->total_amount_promo['format']['view_price']) ? 
                    $_basket->total_amount_promo['format']['view_price']. '&nbsp;' . $_basket->total_amount_promo['currency']['suffix']:
                    $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'])
            ]
        ];

        return $_response;
    }

    public function remove_product_in_basket(Request $request)
    {
        $_product = $request->get('e');
        $_method = $request->get('delivery_method', 'pickup');
        $_keys = explode('::', $_product);
        $_basket = app('basket');
        $_basket->setDelivery($_method);
        $_basket_products = $_basket->data;
        $_remove_product = NULL;
        if($_keys[2] == 'promo_code') $_keys[2] = 0;
        if (isset($_basket_products[$_keys[0]][$_keys[1]][$_keys[2]])) {
            $_remove_product = Product::from('shop_products as p')
                ->leftJoin('shop_product_prices as sp', 'sp.product_id', '=', 'p.id')
                ->where('sp.id', $_keys[0])
                ->select([
                    'p.id',
                    'p.sku',
                    'p.title',
                    'sp.base_price as price',
                ])
                ->remember(REMEMBER_LIFETIME)
                ->first();
            $_remove_product_composition = $_basket_products[$_keys[0]][$_keys[1]][$_keys[2]];
            $_remove_product->quantity = $_remove_product_composition['quantity'];
            if ($_remove_product_composition['composition']) {
                foreach ($_remove_product_composition['composition'] as $_c) {
                    foreach ($_c as $__c) {
                        $_remove_product->price += $__c['amount'];
                    }
                }
            }
        }
        if (isset($_basket_products[$_keys[0]][$_keys[1]][$_keys[2]])) unset($_basket_products[$_keys[0]][$_keys[1]][$_keys[2]]);
        if (isset($_basket_products[$_keys[0]][$_keys[1]]) && !count($_basket_products[$_keys[0]][$_keys[1]])) unset($_basket_products[$_keys[0]][$_keys[1]]);
        if (isset($_basket_products[$_keys[0]]) && !count($_basket_products[$_keys[0]])) unset($_basket_products[$_keys[0]]);
        if (count($_basket_products)) {

            $_basket->bSave($_basket_products);
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#form-checkout-order-products',
                    'data'   => $_basket->show_checkout_products($_basket)
                ]
            ];

             /** updating prices in the basket  */
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#checkout-order-total-amount2 .price-amount',
                    'data'   => $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix']
                ]
            ];

            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#checkout-order-total-amount .price-amount',
                    'data'   => (
                        !empty($_basket->total_amount_promo['format']['view_price']) ? 
                        $_basket->total_amount_promo['format']['view_price']. '&nbsp;' . $_basket->total_amount_promo['currency']['suffix']:
                        $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'])
                ]
            ];

            /* $_response['commands'][] = [
                 'command' => 'replaceWith',
                 'options' => [
                     'target' => '#form-checkout-order',
                     'data'   => $_basket->show_checkout_form($_basket)
                 ]
             ];*/
            $_basket_total_output = '<h6>' . trans('forms.labels.checkout.total_amount_3', [
                    'product' => plural_string($_basket->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'),
                    'amount'  => '&nbsp;&nbsp;</h6><span class="price-amount">' . $_basket->amount['format']['view_price'] . '&nbsp;' . $_basket->amount['currency']['suffix'] . '</span>'
                ]);
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#checkout-order-total-amount',
                    'data'   => $_basket_total_output
                ]
            ];
            //            $_template_target_product = ".product-id-{$_product}";
            //            $_response['commands'][] = [
            //                'command' => 'removeClass',
            //                'options' => [
            //                    'target' => $_template_target_product,
            //                    'data'   => 'add-basket'
            //                ]
            //            ];
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#basket-box a',
                    'data'   => '<img uk-img="data-src:template/images/icon-shop.svg" alt="" width="69" height="37" uk-svg><span class="icon-basket uk-flex uk-flex-middle uk-flex-center">' . $_basket->quantity_in . '</span>',
                ]
            ];
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#checkout-order-delivery-amount',
                    'data'   => $_basket->showDeliveryString() ? : ''
                ]
            ];
        } else {
            $_basket->bClear();
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#form-checkout-order-products',
                    'data'   => '<div class="uk-alert uk-alert-warning">' . trans('frontend.basket_is_empty') . '</div>'
                ]
            ];
            $_response['commands'][] = [
                'command' => 'redirect',
                'options' => [
                    'url' => LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/'),
                    //                    'time' => 500
                ]
            ];
            $_response['commands'][] = [
                'command' => 'remove',
                'options' => [
                    'target' => '#form-checkout-order, #emptying-basket, #checkout-order-total-amount, #checkout-order-delivery-amount',
                ]
            ];

            //                $_response['commands'][] = [
            //                    'command' => 'removeClass',
            //                    'options' => [
            //                        'target' => '#basket-box',
            //                        'data'   => 'uk-visible'
            //                    ]
            //                ];
            //                $_response['commands'][] = [
            //                    'command' => 'addClass',
            //                    'options' => [
            //                        'target' => '#basket-box',
            //                        'data'   => 'uk-hidden'
            //                    ]
            //                ];
            //                $_response['commands'][] = [
            //                    'command' => 'html',
            //                    'options' => [
            //                        'target' => '#basket-box a',
            //                        'data'   => ''
            //                    ]
            //                ];
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#basket-box a',
                    'data'   => '',
                ]
            ];
            $_response['commands'][] = [
                'command' => 'removeClass',
                'options' => [
                    'target' => '#basket-box a',
                    'data'   => 'not-empty'
                ]
            ];
            //                $_response['commands'][] = [
            //                    'command' => 'removeClass',
            //                    'options' => [
            //                        'target' => '#basket-box',
            //                        'data'   => 'uk-visible'
            //                    ]
            //                ];
        }
        if ($_remove_product) {
            $_response['commands'][] = [
                'command' => 'eval',
                'options' => [
                    'data' => "if (typeof gtag == 'function'){gtag('event', 'remove_from_cart', {items: [{id:'{$_remove_product->sku}', name: '{$_remove_product->getTranslation('title', 'ua')}', quantity: {$_remove_product->quantity}, price: {$_remove_product->price}}]});}"
                ]
            ];
        }
        $_gifts = Gift::getInfo();
        if ($_gifts) {
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#gifts-box',
                    'data'   => \view('frontend.default.shops.gifts', ['gifts' => $_gifts])
                        ->render(function ($view, $content) {
                            return clear_html($content);
                        })
                ]
            ];
        }

        return $_response;
    }

    public function emptying_basket(Request $request)
    {
        $_basket = Basket::init();
        $_basket->bClear();
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-products',
                'data'   => '<div class="uk-alert uk-alert-warning">' . trans('frontend.basket_is_empty') . '</div>'
            ]
        ];

        $_response['commands'][] = [
            'command' => 'redirect',
            'options' => [
                'url' => LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/'),
                //                                    'time' => 500
            ]
        ];

        $_response['commands'][] = [
            'command' => 'text',
            'options' => [
                'target' => '#checkout-order-total-amount',
                'data'   => ''
            ]
        ];
        $_response['commands'][] = [
            'command' => 'remove',
            'options' => [
                'target' => '#form-checkout-order, #emptying-basket, #checkout-order-total-amount, #checkout-order-delivery-amount',
            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#basket-box a',
                'data'   => trans('shop.labels.basket', ['amount' => 0]) . '<img uk-img="data-src:template/images/icon-shop.svg" alt="" width="69" height="37" uk-svg>',
            ]
        ];
        $_response['commands'][] = [
            'command' => 'removeClass',
            'options' => [
                'target' => '#basket-box a',
                'data'   => 'not-empty'
            ]
        ];

        //            $_response['commands'][] = [
        //                'command' => 'removeClass',
        //                'options' => [
        //                    'target' => '#basket-box',
        //                    'data'   => 'uk-visible'
        //                ]
        //            ];
        //            $_response['commands'][] = [
        //                'command' => 'addClass',
        //                'options' => [
        //                    'target' => '#basket-box',
        //                    'data'   => 'uk-hidden'
        //                ]
        //            ];

        return $_response;
    }

    public function certificate(Request $request)
    {
        $_certificate = $request->input('certificate');
        $_state = $request->input('state') == 'true' ? 1 : 0;
        if (!$_certificate && $_state == 0) {
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('shop.notifications.certificate_empty'),
                    'status' => 'danger',
                ]
            ];
            $_response['commands'][] = [
                'command' => 'addClass',
                'options' => [
                    'target' => '#form-checkout-order-certificate',
                    'data'   => 'uk-form-danger'
                ]
            ];

            return $_response;
        } else {
            $_response['commands'][] = [
                'command' => 'removeClass',
                'options' => [
                    'target' => '#form-checkout-order-certificate',
                    'data'   => 'uk-form-danger'
                ]
            ];
        }
        $_basket_attrs = [
            'delivery' => $request->get('delivery', 'delivery')
        ];
        if ($_state) {
            //            Cookie::queue(Cookie::forget('frontPad_certificate'));
            $_basket_attrs['certificateClear'] = TRUE;
            $_response['commands'][] = [
                'command' => 'removeClass',
                'options' => [
                    'target' => '#certificate-box button[name=certificate]',
                    'data'   => 'certificate-used'
                ]
            ];
            $_response['commands'][] = [
                'command' => 'text',
                'options' => [
                    'target' => '#certificate-box button[name=certificate]',
                    'data'   => trans('forms.buttons.checkout.use')
                ]
            ];
        } else {
            $_fp = NULL;
            try {
                //                if ($_certificate) $_fp = Frontpad::getCertificate($_certificate);
            } catch (\Exception $e) {
                report($e);
            }
            if ($_fp) {
                $_fp['certificate'] = $_certificate;
                //                Cookie::queue(Cookie::make('frontPad_certificate', json_encode($_fp), 15));
                $_basket_attrs['certificate'] = $_fp;
                $_response['commands'][] = [
                    'command' => 'addClass',
                    'options' => [
                        'target' => '#certificate-box button[name=certificate]',
                        'data'   => 'certificate-used'
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'text',
                    'options' => [
                        'target' => '#certificate-box button[name=certificate]',
                        'data'   => trans('forms.buttons.checkout.clear')
                    ]
                ];
            }
        }
        $_basket = Basket::init($_basket_attrs);
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-products',
                'data'   => $_basket->show_checkout_products($_basket)
            ]
        ];
        $_response['commands'][] = [
            'command' => 'text',
            'options' => [
                'target' => '#certificate-box button[name=certificate]',
                'data'   => $_state ? trans('forms.buttons.checkout.use') : trans('forms.buttons.checkout.clear')
            ]
        ];
        $_response['commands'][] = [
            'command' => $_state ? 'removeClass' : 'addClass',
            'options' => [
                'target' => '#certificate-box button[name=certificate]',
                'data'   => 'certificate-used'
            ]
        ];
        if (is_null($_basket->certificate)) {
            $_response['commands'][] = [
                'command' => 'val',
                'options' => [
                    'target' => '#form-checkout-order-certificate',
                    'data'   => ''
                ]
            ];
        }
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#certificate-box .description-certificate',
                'data'   => $_basket->certificate ? $_basket->certificate['application'] : ''
            ]
        ];
        $_basket_total_output = trans('forms.labels.checkout.total_amount_3', [
            'product' => plural_string($_basket->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'),
            'amount'  => '&nbsp;&nbsp;<span class="price-amount">' . $_basket->amount['format']['view_price'] . '</span>&nbsp;&nbsp;<span class="currency-amount">' . $_basket->amount['currency']['suffix'] . '</span>'
        ]);
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount',
                'data'   => $_basket_total_output
            ]
        ];
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-delivery-amount',
                'data'   => $_basket->showDeliveryString()
            ]
        ];

        return $_response;
    }
    
    /** checks the promo code */
    public function ispromocode(Request $request){
        $_code = $request->input('code');
        $_val = $request->input('val');
        //$_item = Stock::whereRaw('lower(code) = ?',mb_strtolower($_code))->where('status',1)->where("date_to",">=",date('Y-m-d'))->first();

        if($_val==1){
            if(empty($_code)){
                echo json_encode(array("status"=>false,"mess"=>trans('shop.notifications.not_no_text_promo_code')));

            }else{    
                $_item = Stock::whereRaw('lower(code) = ?',mb_strtolower($_code))->first();

                if(empty($_item)){
                    echo json_encode(array("status"=>false,"mess"=>trans('shop.notifications.not_no_promo_code')));
                
                }else if(empty($_item->status)){
                    echo json_encode(array("status"=>false,"mess"=>trans('shop.notifications.not_no_active_promo_code')));

                }else if(empty($_item->date_to >= date('Y-m-d'))){    
                    echo json_encode(array("status"=>false,"mess"=>trans('shop.notifications.not_no_date_active_promo_code')));
                    
                }else{    
    
                    $details = json_decode($_item->details,1);
                    $details['type'] = $_item->type;
                    $details['date_to'] = $_item->date_to;
                    $details['title'] = $_item->title;
                    $details['code'] = $_item->code;

                    Basket::promoCodeSave('promo_code',json_encode($details));

                    echo json_encode(array("status"=>true,"mess"=>trans('shop.notifications.good_active_promo_code')));
                 }
            }
        }else{
            Basket::promoCodeSave('promo_code','');
            echo json_encode(array("status"=>false,"mess"=>trans('shop.notifications.good_not_active_promo_code')));
        }
    }
    public function addpromocode(Request $request){
       
        $res = json_decode($request->input('data'),1);

        //print_r($res['mess']);

        if(!empty($res['mess'])){
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => $res['mess'],
                    'status' => 'danger',
                ]
            ];
        }
       
        $basket = Basket::init();
 
        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount2 .price-amount',
                'data'   => $basket->amount['format']['view_price'] . '&nbsp;' . $basket->amount['currency']['suffix']
            ]
        ];

        $_response['commands'][] = [
            'command' => !empty( $basket->total_amount_promo['format']['view_price'])&&!empty($basket->promo_code['type'])&&$basket->promo_code['type']=='all_basket' ? 'removeClass' : 'addClass',
            'options' => [
                'target' => '#checkout-order-total-amount2',
                'data'   => 'uk-hidden'
            ]
        ];

        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-amount .price-amount',
                'data'   => (
                    !empty($basket->total_amount_promo['format']['view_price']) ? 
                    $basket->total_amount_promo['format']['view_price']. '&nbsp;' . $basket->total_amount_promo['currency']['suffix']:
                    $basket->amount['format']['view_price'] . '&nbsp;' . $basket->amount['currency']['suffix'])
            ]
        ];

        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#checkout-order-total-discount .price-amount',
                'data'   => !empty($basket->promo_code['discount'])&&$basket->promo_code['type']=='all_basket' ? $basket->promo_code['discount']." %": NULL
            ]
        ];


        $_response['commands'][] = [
            'command' => !empty($basket->total_amount_promo['format']['view_price'])&&!empty($basket->promo_code['type'])&&$basket->promo_code['type']=='all_basket' ? 'removeClass' : 'addClass',
            'options' => [
                'target' => '#checkout-order-total-discount',
                'data'   => 'uk-hidden'
            ]
        ];

        $_response['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#basket-box a span',
                'data'   => $basket->quantity_in,
            ]
        ];
        
        $_response['commands'][] = [
            'command' => 'replaceWith',
            'options' => [
                'target' => '#form-checkout-order-products',
                'data'   => $basket->show_checkout_products($basket)
            ]
        ];

        if($res['status']){
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '.btn-submit-promo',
                    'data'   => trans('forms.buttons.checkout.cancel')
                ]
            ];
            $_response['commands'][] = [
                'command' => 'val',
                'options' => [
                    'target' => '.btn-submit-promo',
                    'data'   => '0'
                ]
            ];
            $_response['commands'][] = [
                'command' => 'eval',
                'options' => [
                    'data' => '$("#form-checkout-order-stock").attr("disabled","disabled");'
                ]
            ];
        }else{
            $_response['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '.btn-submit-promo',
                    'data'   => trans('forms.buttons.checkout.use')
                ]
            ];
            $_response['commands'][] = [
                'command' => 'val',
                'options' => [
                    'target' => '.btn-submit-promo',
                    'data'   => '1'
                ]
            ];
            $_response['commands'][] = [
                'command' => 'eval',
                'options' => [
                    'data' => '$("#form-checkout-order-stock").removeAttr("disabled");'
                ]
            ];
        }
        

        return $_response;
    }
}
