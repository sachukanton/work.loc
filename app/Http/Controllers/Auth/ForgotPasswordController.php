<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Library\BaseController;
    use App\Models\Structure\Page;
    use App\Models\User\User;
    use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class ForgotPasswordController extends BaseController
    {

        use SendsPasswordResetEmails;

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        public function showLinkRequestForm()
        {
            $_wrap = wrap()->get();
            $_template = [
                "frontend.{$this->deviceTemplate}.user.email",
                'frontend.default.user.email'
            ];
            $_item = new Page();
            $_item->setWrap([
                'seo.robots'      => 'noindex, nofollow',
                'seo.title'       => trans('pages.titles.forgot_email'),
                'page.title'      => trans('pages.titles.forgot_email'),
                'page.style_id'   => 'page-forgot-email-form',
                'seo.url_alias'   => 'register',
                'page.breadcrumb' => collect([
                    [
                        'name'     => trans('pages.titles.home'),
                        'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                        'position' => 1
                    ],
                    [
                        'name'     => trans('pages.titles.forgot_email'),
                        'url'      => NULL,
                        'position' => 2
                    ]
                ]),
                'alias'           => 'register'
            ]);
            $_item->emailFormOutput = User::show_forgot_email_form();
            $_wrap = wrap()->render();

            return View::first($_template, compact('_item', '_wrap'));
        }

        public function sendResetLinkEmail(Request $request)
        {
            $_response = [
                'result'   => FALSE,
                'message'  => NULL,
                'commands' => NULL,
            ];
            $_form = $request->get('form');
            $_validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
            ], [], [
                'email' => trans('forms.fields.profile.email')
            ]);
            if ($_validator->fails()) {
                foreach ($_validator->errors()->messages() as $_field => $_message) {
                    $_response['message'] .= "<div>{$_message[0]}</div>";
                    $_response['commands'][] = [
                        'command' => 'addClass',
                        'options' => [
                            'target' => '#' . generate_field_id($_field, $_form),
                            'data'   => 'error'
                        ]
                    ];
                }
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => $_response['message'],
                        'status' => 'danger'
                    ]
                ];

                return response($_response, 200);
            }
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );
            if ($response == Password::RESET_LINK_SENT) {
                $_response['result'] = TRUE;
                $_response['message'] = trans($response);
            } else {
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => trans($response),
                        'status' => 'danger'
                    ]
                ];
            }

            return response($_response, 200);
        }

    }
