<?php

namespace App\Notifications;

use App\Jobs\StockNotice;
use App\Models\Shop\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class StockProductNotification extends Notification
{

    use Queueable, SerializesModels;

    public $item;
    public $locale;
    public $device;

    public function __construct(Form $form)
    {
        $this->item = $form;
        $this->locale = wrap()->get('locale', env('DEFAULT_LOCALE'));
        $this->device = wrap()->get('device.type', 'pc');
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
            ->view('mail.stock_notice', [
                '_item'          => $this->item,
                '_subject'       => "На сайте появился товар",
                '_site_url'      => config('app.url'),
                '_locale'        => $this->locale,
                '_device'        => $this->device,
                '_site_data'     => $_site_data,
                '_site_contacts' => $_site_contacts,
            ])
            ->subject("На сайте появился товар");

        return $_mail;
    }

}