<?php

namespace App\Notifications;

use App\Models\Shop\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ShopFormNotification extends Notification
{

    use Queueable, SerializesModels;

    public $item;
    public $locale;
    public $device;

    public function __construct(Form $formData)
    {
        $this->item = $formData;
        $this->item->price_view = view_price($formData->price, $formData->price);
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
        $_product = $this->item->_product;
        $_site_data = config("os_seo.settings.{$this->locale}");
        $_site_contacts = contacts_load($this->locale);
        $_mail = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.form_shop', [
                '_item'          => $this->item,
                '_product'       => $_product->id ? _l($_product->title, $_product->generate_url, [
                    'attributes' => ['target' => '_blank'],
                    'full_path'  => TRUE
                ]) : $this->item->product_name,
                '_form'          => $this->item->type,
                '_site_url'      => config('app.url'),
                '_locale'        => $this->locale,
                '_device'        => $this->device,
                '_site_data'     => $_site_data,
                '_site_contacts' => $_site_contacts,
            ])
            ->subject("Отправлена форма \"{$this->item->type}\" #{$this->item->id}");

        return $_mail;
    }
}