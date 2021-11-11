<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\LiqpayRequest;
use App\Models\Shop\Basket;
use App\Notifications\ShopOrderNotification;
use Carbon\Carbon;
use Iiko\Biz\Exception\IikoResponseException;
use Illuminate\Http\Request;
use App\Models\Shop\Order;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use League\Flysystem\Exception;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LiqPayController extends Controller
{
    public function liqpayStatus(LiqpayRequest $request)
    {
        $data = json_decode(base64_decode($request->data));
        $privateKey = config('os_shop.liqpay.private_key');
        $sign = base64_encode(sha1(
            $privateKey .
            $request->data .
            $privateKey,
            1));
        Log::info('LiqPayStatus::request-data' . json_encode($data));
        $_order = Order::findOrfail(substr($data->order_id, 5));
        if ($sign === $request->signature) {
            $_order_status_ok = in_array($data->status, [
                'success',
                'wait_accept'
            ]) ? 1 : 0;
            if ($_order_status_ok) {
                $_order->update([
                    'payment_transaction_number' => $data->payment_id,
                    'payment_transaction_status' => $data->status,
                    'payment_status'             => $_order_status_ok,
                    'status'                     => $_order_status_ok,
                ]);
                Notification::route('mail', env('MAIL_ADMIN_NOTIFICATION'))
                    ->notify(new ShopOrderNotification($_order));
                global $wrap;
                $_basket = Basket::init();
                $iiko = app('iiko');
                $organization = $iiko->OrganizationsApi()->getList()[0];
                $_phone = '+' . preg_replace('/^\+|\D/m', '', $_order->phone);
                $_pre_order_at = $_order->pre_order_at ?? NULL;
                if ($_pre_order_at) {
                    $_pre_order_at = Carbon::parse($_pre_order_at);
                    $_new_at = Carbon::now();
                    $_pre_order_at = $_new_at->diffInMinutes($_pre_order_at) < 90 ? NULL : $_pre_order_at->toDateTimeString();
                }
                $_items = [];
                $_basket->composition->map(function ($p) use (&$_items) {
                    foreach ($p->composition as $_spicy => $_comp) {
                        $_items[] = [
                            'id'     => $p->iiko_id,
                            'code'   => $p->sku,
                            'amount' => (int)$_comp['quantity'],
                            'sum'    => (float)$_comp['price']['original']['price'],
                        ];
                    }
                });
                $_iiko_requestOrder = [
                    'organization' => $organization['id'],
                    'customer'     => [
                        'name'  => $_order->name,
                        'phone' => $_phone,
                    ],
                    'order'        => [
                        'date'         => $_pre_order_at,
                        'items'        => $_items,
                        'phone'        => $_phone,
                        'customerName' => $_order->name,
                        'comment'      => $_order->comment ?? NULL,
                    ],
                    'coupon'       => NULL,
                ];
                if ($_order->delivery_method == 'pickup') {
                    $_iiko_requestOrder['order']['isSelfService'] = TRUE;
                    $_iiko_requestOrder['order']['discountCardTypeId'] = '1b479511-ca7f-4df8-9d64-3dd33b6c00e8';
                } else {
                    $_iiko_requestOrder['order']['address'] = [
                        'city'      => 'Харьков',
                        'street'    => $_order->delivery_address->street,
                        'home'      => $_order->delivery_address->house,
                        'floor'     => $_order->delivery_address->floor,
                        'apartment' => $_order->delivery_address->apartment,
                    ];
                }
                $_iiko_requestOrder['order']['paymentItems'] = [
                    [
                        'paymentType'           => [
                            'code' => 'VISA'
                        ],
                        'sum'                   => $_order->amount_less_discount ? : $_order->amount,
                        'isProcessedExternally' => TRUE,
                        'isExternal'            => $data->status == 'success',
                        'additionalData'        => $data->status == 'success' ? 'Оплата успешна #' . $data->payment_id : 'Платеж требует проверки #' . $data->payment_id,
                    ]
                ];
                try {
                    $_iiko_order = app('iiko')->OrdersApi()->addOrder($_iiko_requestOrder);
                    $_order->update([
                        'rk_order_id'     => $_iiko_order['orderId'],
                        'rk_order_number' => $_iiko_order['number'],
                        'rk_order_sum'    => $_iiko_order['sum'],
                    ]);
                } catch (IikoResponseException $e) {
                    report($e);
                } catch (Exception $e) {
                    report($e);
                }

            }
        } else {
            return response([
                'message' => 'Bad signature',
            ], 401);
        }
    }

    public function paymentResponse(LiqpayRequest $request)
    {
        $data = json_decode(base64_decode($request->data));
        $_order = Order::findOrfail(substr($data->order_id, 5));
        $privateKey = config('os_shop.liqpay.private_key');
        $sign = base64_encode(sha1(
            $privateKey .
            $request->data .
            $privateKey,
            1));
        Log::info('LiqPayResponse::' . json_encode($data));
        Log::info('ORDER::' . json_encode($_order));
        Log::info('SIGNATURE::' . ($sign === $request->signature));
        $_order_status_ok = in_array($data->status, [
            'success',
            'wait_accept'
        ]) ? 1 : 0;
        if (($sign === $request->signature && !$_order_status_ok) || $sign !== $request->signature) {
            $_order->update([
                'status'                     => '-1',
                'payment_transaction_status' => $data->status,
                'payment_status'             => 0,
            ]);
            Notification::route('mail', env('MAIL_ADMIN_NOTIFICATION'))
                ->notify(new ShopOrderNotification($_order));

            return redirect()
                ->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), 'checkout'))
                ->with('commands', json_encode([
                    [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => trans('forms.messages.checkout.failure_payment'),
                            'pos'    => 'top-right',
                        ]
                    ]
                ]));
        } else {
            $_basket = Basket::init();
            $_basket->bClear();
            $_order_amount = $_order->amount_less_discount ? : $_order->amount;
            $_order_amount = view_price($_order_amount, $_order_amount);
            spy("На сайте оставлен заказ товаров <a href='/oleus/shop-orders/{$_order->id}/edit' class='uk-text-bold' target='_blank'>№{$_order->id}</a> на сумму <span class='uk-text-bold'>{$_order_amount['format']['view_price']}</span> {$_order_amount['currency']['suffix']}. Текущий статус заказа <span class='uk-text-uppercase uk-text-bold'>" . trans('shop.status.' . $_order->status) . "</span>. <a href='/oleus/shop-orders/{$_order->id}/edit'>Просмотреть данные заказа</a>.", 'success');

            return redirect()
                ->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), 'checkout-thanks'))
                ->with('order', $_order);
        }
    }
}
