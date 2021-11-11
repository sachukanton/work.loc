<?php

    namespace App\Http\Controllers\Auth;

    use App\Library\BaseController;
    use App\Models\Structure\Page;
    use Illuminate\Foundation\Auth\ResetsPasswords;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class ResetPasswordController extends BaseController
    {

        use ResetsPasswords;

        protected $redirectTo = '/account';

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        public function showResetForm(Request $request, $token = NULL)
        {
            $_template = [
                "frontend.{$this->deviceTemplate}.user.password_reset",
                'frontend.default.user.password_reset'
            ];
            $_item = new Page();
            $_wrap = wrap()->get();
            $_item->setWrap([
                'seo.title'       => trans('frontend.titles.reset_password'),
                'seo.robots'      => 'noindex, nofollow',
                'page.title'      => trans('frontend.titles.reset_password'),
                'alias'           => 'password/reset',
                'page.breadcrumb' => collect([
                    [
                        'name'     => trans('frontend.titles.home'),
                        'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                        'position' => 1
                    ],
                    [
                        'name'     => trans('frontend.titles.reset_password'),
                        'url'      => NULL,
                        'position' => 2
                    ]
                ]),
            ]);
            $_wrap = wrap()->render();

            return View::first($_template, compact('_item', '_wrap', 'token'));
        }

        protected function credentials(Request $request)
        {
            $request->request->add([
                'password_confirmation' => $request->input('password')
            ]);

            return $request->only(
                'email', 'password', 'token', 'password_confirmation'
            );
        }

        protected function validator($data)
        {
            return Validator::make($data, [
                'token'    => 'required',
                'email'    => 'required|string|email',
                'password' => 'required|string|min:8',
                'captcha'  => 'required|reCaptchaV3',
            ], [], [
                'email'    => trans('forms.fields.reset_password.email'),
                'token'    => trans('forms.fields.reset_password.token'),
                'password' => trans('forms.fields.reset_password.password'),
                'captcha'  => trans('forms.fields.captcha'),
            ]);
        }

        public function reset(Request $request)
        {
            $_validator = $this->validator($request->all());
            if ($request->ajax()) {
                $_response = [
                    'result'   => FALSE,
                    'message'  => NULL,
                    'commands' => NULL,
                ];
                $_form = $request->get('form');
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => "#{$_form} input",
                        'data'   => 'error'
                    ]
                ];
                if ($_validator->fails()) {
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $_field_name = str_replace('.', '_', $_field);
                        $_response['message'] .= "<div>{$_message[0]}</div>";
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($_field_name, $_form),
                                'data'   => 'error'
                            ]
                        ];
                    }
                    $_response['commands'][] = [
                        'command' => 'val',
                        'options' => [
                            'target' => '#' . generate_field_id('password', $_form),
                            'data'   => ''
                        ]
                    ];
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => $_response['message'],
                            'status' => 'danger'
                        ]
                    ];
                } else {
                    $response = $this->broker()->reset(
                        $this->credentials($request), function ($user, $password) {
                        $this->resetPassword($user, $password);
                    });
                    if ($response != Password::PASSWORD_RESET) {
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => generate_field_id('email', $_form),
                                'data'   => 'error'
                            ]
                        ];
                        $_response['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => '<div>' . trans($response) . '</div>',
                                'status' => 'danger'
                            ]
                        ];
                    } else {
                        $_response['result'] = TRUE;
                        $_response['commands'][] = [
                            'command' => 'redirect',
                            'options' => [
                                'url' => $this->redirectTo
                            ]
                        ];
                    }
                }

                return response($_response, 200);
            }
        }

    }
