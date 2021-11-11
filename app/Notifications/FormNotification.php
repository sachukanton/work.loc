<?php

namespace App\Notifications;

use App\Models\Form\FormsData;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class FormNotification extends Notification
{

    use Queueable, SerializesModels;

    public $item;
    public $locale;
    public $device;

    public function __construct(FormsData $formsData)
    {
        $this->item = $formsData;
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
        $_form = $this->item->_form;
        $_site_data = config("os_seo.settings.{$this->locale}");
        $_site_contacts = contacts_load($this->locale);
        $_view = choice_template([
            "mail.form_constructor_{$_form->id}",
            'mail.form_constructor'
        ]);
        $_mail = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view($_view, [
                '_item'          => $this->item,
                '_subject'       => $_form->email_subject,
                '_site_url'      => config('app.url'),
                '_locale'        => $this->locale,
                '_device'        => $this->device,
                '_site_data'     => $_site_data,
                '_site_contacts' => $_site_contacts,
            ])
            ->subject($_form->email_subject);
        if ($_form->user_email_field_id && isset($this->item->data->{$_form->user_email_field_id}->data) && filter_var($this->item->data->{$_form->user_email_field_id}->data, FILTER_VALIDATE_EMAIL)) {
            $_mail->cc($this->item->data->{$_form->user_email_field_id}->data);
        }

        return $_mail;
    }

}