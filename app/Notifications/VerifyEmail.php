<?php

namespace App\Notifications;

use App\Models\User\User;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends VerifyEmailBase
{

    use Queueable;

    public $user;
    public $subject;
    public $locale;
    public $device;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->subject = trans('mail.subjects.verify_email');
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
            ->view('mail.registered_user', [
                '_user'                   => $this->user,
                '_subject'                => $this->subject,
                '_verification_email_url' => $this->verificationUrl($notifiable),
                '_site_url'               => config('app.url'),
                '_locale'                 => $this->locale,
                '_device'                 => $this->device,
                '_site_data'              => $_site_data,
                '_site_contacts'          => $_site_contacts,
            ])
            ->subject($this->subject);
    }

}