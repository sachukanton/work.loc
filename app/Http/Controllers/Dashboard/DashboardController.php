<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\ImportXMLReader;
use App\Jobs\ImportXML;
use App\Jobs\ProductsImportPriceViewUpdate;
use App\Jobs\RecalculatePart;
use App\Library\Dashboards;
use App\Library\Interaction1C;
use App\Models\Components\Journal;
use App\Models\File\File;
use App\Models\Form\BuyCheaper;
use App\Models\Form\BuyIncomplete;
use App\Models\Form\FormsData;
use App\Models\Review;
use App\Models\Seo\Redirect;
use App\Models\Seo\SearchIndex;
use App\Models\Shop\Basket;
use App\Models\Shop\Category;
use App\Models\Shop\Form;
use App\Models\Shop\Order;
use App\Models\Shop\OrderProduct;
use App\Models\Shop\OrderRegular;
use App\Models\Shop\Price;
use App\Models\Shop\Product;
use App\Notifications\ErrorImportFileNotification;
use App\Notifications\ShopOrderNotification;
use App\Notifications\StockProductNotification;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Iiko\Biz\Exception\IikoResponseException;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Kolirt\Frontpad\Facade\Frontpad;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{

    use Authorizable;
    use Notifiable;
    use Dashboards;

    public $user;

    public function __construct()
    {
        parent::__construct();
        $this->middleware([
            'permission:access_dashboard'
        ]);
        $this->titles = [
            'index' => 'Панель управления',
        ];
    }

    public function dashboard()
    {
        $_user = Auth::user();
        $_others = NULL;
        $_wrap = $this->render([
            'seo.title' => $this->titles['index']
        ]);

        if ($_user->hasPermissionTo('journal_read')) {
            $_others['journal'] = Journal::orderByDesc('id')
                ->take(10)
                ->select([
                    'type',
                    'message',
                    'created_at'
                ])
                ->get();
            if ($_others['journal']->isNotEmpty()) {
                $_others['journal']->transform(function ($_item) {
                    $_item->class = 'primary';
                    if ($_item->type == 'warning') $_item->class = 'warning';
                    if ($_item->type == 'error') $_item->class = 'danger';
                    if ($_item->type == 'success') $_item->class = 'success';

                    return $_item;
                });
            }
        }
        if ($_user->hasPermissionTo('shop_orders_read')) {
            $_others['new_orders'] = Order::get_new_orders();
            $_others['last_complete_orders'] = Order::get_complete_orders();
        }

        return view('backend.main.index', compact('_others', '_wrap'));
    }

    public function polygon()
    {

        $iiko = app('iiko');
        $organization = $iiko->OrganizationsApi()->getList()[0];

        dd($organization);

        $order = Order::find(541);

        $this->item = $order;
        $this->locale = wrap()->get('locale', env('DEFAULT_LOCALE'));
        $this->device = wrap()->get('device.type', 'pc');
        $this->item->amount_view = view_price($this->item->amount, $this->item->amount);
        $this->item->amount_less_discount_view = view_price($this->item->amount_less_discount, $this->item->amount_less_discount);
        $this->item->discount_view = view_price($this->item->discount, $this->item->discount);
        $this->item->products = $this->item->_products->transform(function ($_product) {
            $_product->price_view = view_price($_product->price, $_product->price);
            $_product->amount_view = view_price($_product->amount, $_product->amount);
            $_product->amount_less_discount_view = view_price($_product->amount_less_discount, $_product->amount_less_discount);

            return $_product;
        });

        $_site_data = config("os_seo.settings.ru");
        $_site_contacts = contacts_load('ru');

        return view('mail.orders', [
            '_item'          => $this->item,
            '_subject'       => 'Оформлен заказ #' . $this->item->id,
            '_site_url'      => config('app.url'),
            '_locale'        => 'ru',
            '_device'        => 'pc',
            '_site_data'     => $_site_data,
            '_site_contacts' => $_site_contacts,
        ]);
        dd($order);


        exit();

        $_basket = app('basket');
        //        dd($_basket->total_amount_without_modification);
        $iiko = app('iiko');
        $organization = $iiko->OrganizationsApi()->getList()[0];

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
                'name'  => 'Admin',
                'phone' => '+380997777777',
            ],
            'order'        => [
                'date'          => NULL,
                'items'         => $_items,
                'phone'         => '+380997777777',
                'customerName'  => 'Admin',
                'comment'       => NULL,
                'isSelfService' => TRUE,
            ],
            'coupon'       => NULL,
        ];

        try {
            $_check_create_order = app('iiko')->OrdersApi()->checkCreate($_iiko_requestOrder);
        } catch (IikoResponseException $e) {
            echo 'error';
        }


        dd($_check_create_order);

        //        Mail::raw('text', function ($mail) {
        //            $mail->to('eredjepov.aziz@gmail.com');
        //        });


        //        $discount = $iiko->OrdersApi()->getCombosInfo($organization['id']);

        dd($organization);

        $_test_order = [
            //            'organization' => $organization['id'],
            'organization' => 'e3620000-7b0f-0696-02ac-08d88706a5b9',
            'customer'     => [
                'name'  => 'test',
                'phone' => '380991234567',
            ],
            'order'        => [
                'date'          => Carbon::now()->toDateTimeString(),
                'items'         => [
                    [
                        'id'     => '99abe4f5-b774-4301-92da-45faab7cfc49',
                        'code'   => '00068',
                        'name'   => 'Маки с тунцом',
                        'amount' => 5,
                        'sum'    => 57,
                    ]
                ],
                'phone'         => '380991234567',
                'customerName'  => 'test',
                'isSelfService' => TRUE,
                //                'address'      => [
                //                    'city'   => 'Харьков',
                //                    'street' => 'пер. Плехановский',
                //                    'home'   => '4',
                //                ],
                'comment'       => 'ТЕСТОВЫЙ ЗАКАЗ',
                //                'paymentItems' => $data['order']['paymentItems'],
            ],
            'coupon'       => $data['promo'] ?? NULL,
        ];

        $problems = app('iiko')->OrdersApi()->checkCreate($_test_order);

        dd($problems);

        //        $problems = $iiko->OrdersApi()->addOrder($_test_order);

        //        dd($_test_order, $problems);
        //

        exit();
    }

    public function artisan($command, $target)
    {
        try {
            Artisan::call("{$target}:{$command}");
            Session::flash('notice', [
                'status'  => 'success',
                'message' => 'Команда выполнена'
            ]);
        } catch (\Exception $exception) {
            Session::flash('notice', [
                'status'  => 'danger',
                'message' => 'Возникла ошибка выполнения скрипта'
            ]);
        }

        return redirect()
            ->back();
    }

}
