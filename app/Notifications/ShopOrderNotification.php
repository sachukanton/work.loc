<?php

namespace App\Notifications;

use App\Exports\OrderExport;
use App\Models\Shop\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ShopOrderNotification extends Notification
{

    use Queueable, SerializesModels;

    public $item;
    public $locale;
    public $device;

    public function __construct(Order $order)
    {
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
    }

    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    public function toMail($notifiable)
    {
        $_site_data = config("os_seo.settings.{$this->locale}");
        $_site_contacts = contacts_load($this->locale);
        request()->request->add([
            'order_id' => $this->item->id
        ]);
        $_mail = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.orders', [
                '_item'          => $this->item,
                '_subject'       => 'Оформлен заказ #'. $this->item->id,
                '_site_url'      => config('app.url'),
                '_locale'        => $this->locale,
                '_device'        => $this->device,
                '_site_data'     => $_site_data,
                '_site_contacts' => $_site_contacts,
            ])
            ->subject("Оформлен заказ #{$this->item->id}")
            ->attach(
                Excel::download(
                    new OrderExport(),
                    "order_{$this->item->id}.xlsx"
                )->getFile(), [
                    'as' => "order_{$this->item->id}.xlsx"
                ]
            );

        return $_mail;
    }

}
