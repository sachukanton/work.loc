<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\LiqPay;
use App\Models\File\File;
use App\Models\Pharm\PharmPharmacy;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Kolirt\Frontpad\Facade\Frontpad;

class Order extends BaseModel
{

    protected $table = 'orders';
    protected $guarded = [];
    const ORDER_STATUS = [
        -1 => 'shop.status.-1',
        0  => 'shop.status.0',
        1  => 'shop.status.1',
        2  => 'shop.status.2',
        3  => 'shop.status.3',
        4  => 'shop.status.4',
    ];
    protected $dates = [
        'pre_order_at'
    ];
    const LIQPAY_STATUS = [
        'error'        => 'Неуспешный платеж. Некорректно заполнены данные',
        'failure'      => 'Неуспешный платеж',
        'reversed'     => 'Платеж возвращен',
        'subscribed'   => 'Подписка успешно оформлена',
        'success'      => 'Успешный платеж',
        'unsubscribed' => 'Подписка успешно деактивирована',
        'processing'   => 'Платеж обрабатывается',
        'wait_sender'  => 'Ожидается подтверждение оплаты клиентом в приложении Privat24/SENDER',
        'wait_accept'  => 'Деньги с клиента списаны, но магазин еще не прошел проверку. Если магазин не пройдет активацию в течение 180 дней, платежи будут автоматически отменены',
    ];

    /**
     * Relationships
     */
    public function _products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function _attach_file()
    {
        return $this->hasOne(File::class, 'id', 'attach_file');
    }

    public function _gift()
    {
        return $this->hasOne(Gift::class, 'id', 'gift_id')
            ->withDefault();
    }

    /**
     * Attributes
     */
    public function getUserFullNameAttribute()
    {
        $_response = collect([]);
        if ($this->surname) $_response->push($this->surname);
        if ($this->name) $_response->push($this->name);
        if ($this->patronymic) $_response->push($this->patronymic);

        return $_response->implode(' ');
    }

    public function getFormatAmountAttribute()
    {
        $_response = [
            'amount'               => view_price($this->amount, $this->amount),
            'amount_less_discount' => view_price($this->amount_less_discount, $this->amount_less_discount),
        ];

        return $_response;
    }

    public function getFormatPhoneAttribute()
    {
        $_phone = str_replace('&nbsp;', '', html_entity_decode($this->phone));
        $_phone = str_replace(' ', '', $_phone);

        return '<a href="tel:' . $_phone . '">' . $this->phone . '</a>';
    }

    public function getFormationAddressAttribute()
    {
        $_formation = NULL;
        if ($this->delivery_address->street) $_formation[] = $this->delivery_address->street;
        if ($this->delivery_address->house) $_formation[] = "дом {$this->delivery_address->house}";
        if ($this->delivery_address->entrance) $_formation[] = "подъезд {$this->delivery_address->entrance}";
        if ($this->delivery_address->floor) $_formation[] = "этаж {$this->delivery_address->floor}";
        if ($this->delivery_address->apartment) $_formation[] = "квартира {$this->delivery_address->apartment}";

        return $_formation ? implode(', ', $_formation) : NULL;
    }

    public function getDeliveryAddressAttribute()
    {
        $_delivery_address = $this->attributes['delivery_address'] ?? NULL;
        if ($_delivery_address && is_json($_delivery_address)) {
            return json_decode($_delivery_address);
        }

        return (object)[
            'street'    => NULL,
            'house'     => NULL,
            'entrance'  => NULL,
            'floor'     => NULL,
            'apartment' => NULL,
        ];
    }

    public function setDeliveryAddressAttribute($value = NULL)
    {
        $_address = array_merge([
            'street'    => NULL,
            'house'     => NULL,
            'entrance'  => NULL,
            'floor'     => NULL,
            'apartment' => NULL,
        ], $value);
        $this->attributes['delivery_address'] = json_encode($_address);
    }

    /**
     * Others
     */
    public static function get_new_orders()
    {
        return self::with([
            '_products',
        ])
            ->where('status', '<', 3)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(15)
            ->get();
    }

    public static function get_complete_orders($take = 25)
    {
        return self::with([
            '_products',
        ])
            ->where('status', '>', 2)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take($take)
            ->get();
    }

    public function setDataToIiko()
    {

    }

    public function getLiqpayApiForm($sum = 0)
    {
        $sum = $sum ? $sum : $this->amount;
        if (config('os_shop.liqpay.public_key')) {
            $publicKey = config('os_shop.liqpay.public_key');
            $privateKey = config('os_shop.liqpay.private_key');
            $locale = app()->getLocale();
            $localePath = $locale != DEFAULT_LOCALE ? "/{$locale}" : '';
            $params = [
                'action'      => 'pay',
                'amount'      => $sum,
                'currency'    => 'UAH',
                'description' => 'Оплата заказа еды на сайте.',
                'order_id'    => substr(md5(rand(0, 10000)), 0, 5) . $this->id,
                'result_url'  => 'https://puc.com.ua' . $localePath . _r('payment.response'),
                'server_url'  => 'https://puc.com.ua' . $localePath . _r('payment.status'),
                'version'     => '3',
                'language'    => app()->getLocale(),
            ];

            return (new LiqPay($publicKey, $privateKey))->cnb_form($params);
        }

        return NULL;
    }
}
