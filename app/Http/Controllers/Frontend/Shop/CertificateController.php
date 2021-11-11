<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Library\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Kolirt\Frontpad\Facade\Frontpad;


class CertificateController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request, $certificate)
    {
        $_fp = NULL;
        try {
            $_fp = Frontpad::getCertificate($certificate);
        } catch (\Exception $e) {
            report($e);
        }
        if ($_fp) {
            $_fp['certificate'] = $certificate;
            Cookie::queue(Cookie::make('frontPad_certificate', json_encode($_fp), 15));
            $_message = variable('modal_message_application_certificate');

            return redirect()
                ->to(isset($_fp['product_id']) ? '/checkout' : '/')
                ->with('commands', json_encode([
                    [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => '<button class="uk-modal-close-outside" type="button" uk-close></button><div class="uk-padding message">' . $_message . '</div>',
                            'classDialog' => 'uk-margin-auto-vertical',
                            'classModal'  => 'uk-flex-top',
                            'id'          => 'modal-application-certificate'
                        ]
                    ]
                ]));
        } else {
            $_message = variable('modal_message_not_application_certificate');

            return redirect()
                ->to('/')
                ->with('commands', json_encode([
                    [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => '<button class="uk-modal-close-outside" type="button" uk-close></button><div class="uk-padding message">' . $_message . '</div>',
                            'classDialog' => 'uk-margin-auto-vertical',
                            'classModal'  => 'uk-flex-top',
                            'id'          => 'modal-application-certificate'
                        ]
                    ]
                ]));
        }
    }

}
