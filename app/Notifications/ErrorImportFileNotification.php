<?php

namespace App\Notifications;

use App\Jobs\StockNotice;
use App\Models\Shop\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ErrorImportFileNotification extends Notification
{

    use Queueable, SerializesModels;

    public $message;
    public $subject;

    public function __construct($message, $subject)
    {
        $this->message = $message;
        $this->subject = $subject;
    }

    public function via($notifiable)
    {
        return [
            'mail'
        ];
    }

    public function toMail($notifiable)
    {
        $_mail = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.error_import_file', [
                '_subject' => $this->subject,
                '_message' => $this->message,
            ])
            ->subject($this->subject)
            ->attach(storage_path('app/last_download_file.xml'), [
                'as'   => 'last_download_file.xml',
                'mime' => 'application/xml'
            ]);

        return $_mail;
    }

}