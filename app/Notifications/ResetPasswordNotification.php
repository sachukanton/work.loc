<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    use Queueable;

    public $token;
    public $subject;
    public $locale;
    public $device;

    public function __construct($token)
    {
        $this->token = $token;
        $this->subject = trans('mail.subjects.reset_password');
        $this->locale = wrap()->get('locale', env('LOCALE'));
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
        if (static::$toMailCallback) return call_user_func(static::$toMailCallback, $notifiable);
        $_site_data = config("os_seo.settings.{$this->locale}");
        $_site_contacts = contacts_load($this->locale);

        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.reset_password', [
                '_subject'       => $this->subject,
                '_site_url'      => config('app.url'),
                '_site_name'     => config("os_seo.settings.{$this->locale}.site_name"),
                '_token_url'     => config('app.url') . _r('password.reset') . "/{$this->token}",
                '_locale'        => $this->locale,
                '_device'        => $this->device,
                '_site_data'     => $_site_data,
                '_site_contacts' => $_site_contacts,
            ])
            ->subject($this->subject);
    }

}