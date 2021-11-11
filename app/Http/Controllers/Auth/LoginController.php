<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\Structure\Page;
    use App\Models\User\User;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;
    use Illuminate\Validation\ValidationException;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class LoginController extends Controller
    {

        use AuthenticatesUsers;

        protected $redirectTo = 'account';
        protected $username;
        protected $typeLoginField;
        protected $maxAttempts = 3;

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest')
                ->except('logout');
            $this->username = $this->findUsername();
            $this->typeLoginField = $this->typeFieldUsername();
        }

        public function username()
        {
            return $this->username;
        }

        public function findUsername()
        {
            $_field_type = 'email';
            if ($_field_email_or_name = request()->input('login.email_or_name')) {
                $_field_type = str_contains($_field_email_or_name, '@') ? 'email' : 'name';
            }
            request()->merge([$_field_type => $_field_email_or_name]);

            return $_field_type;
        }

        public function typeFieldUsername()
        {
            $_field_type = 'email';
            if ($_field_email_or_name = request()->input('login.email_or_name')) {
                $_field_type = str_contains($_field_email_or_name, '@') ? 'email' : 'name';
            }

            return $_field_type;
        }

        public function showLoginForm()
        {
            $_wrap = wrap()->get();
            $_template = [
                "frontend.{$this->deviceTemplate}.user.login",
                'frontend.default.user.login'
            ];
            $_item = new Page();
            $_item->setWrap([
                'seo.robots'      => 'noindex, nofollow',
                'seo.title'       => trans('pages.titles.login'),
                'page.title'      => trans('pages.titles.login'),
                'seo.url_alias'   => 'login',
                'page.style_id'   => 'page-login-form',
                'page.breadcrumb' => collect([
                    [
                        'name'     => trans('pages.titles.home'),
                        'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                        'position' => 1
                    ],
                    [
                        'name'     => trans('pages.titles.login'),
                        'url'      => NULL,
                        'position' => 2
                    ]
                ]),
                'alias'           => 'login'
            ]);
            $_item->loginFormOutput = User::show_login_form();
            $_wrap = wrap()->render();

            return View::first($_template, compact('_item', '_wrap'));
        }

        public function login(Request $request)
        {
            $_form = $request->get('form');
            $_response = NULL;
            if ($request->ajax()) {
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => "#{$_form} input",
                        'data'   => 'uk-form-danger'
                    ]
                ];
                $_validator = Validator::make($request->all(), [
                    'login.email_or_name' => $this->typeLoginField == 'email' ? 'required|string|email' : 'required|string',
                    'login.password'      => 'required|string',
           //         'captcha'             => 'required|reCaptchaV3',
                ], [], [
                    'login.email_or_name' => trans('forms.fields.login.email_or_name'),
                    'login.password'      => trans('forms.fields.login.password'),
                    'captcha'             => trans('forms.fields.captcha'),
                ]);
                if ($_validator->fails()) {
                    $_messages = NULL;
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $_messages .= "<div>{$_message[0]}</div>";
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($_field, $_form),
                                'data'   => 'uk-form-danger'
                            ]
                        ];
                    }
                    $_response['commands'][] = [
                        'command' => 'val',
                        'options' => [
                            'target' => "#{$_form} input[type='password']",
                            'data'   => ''
                        ]
                    ];
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => $_messages,
                            'status' => 'danger'
                        ]
                    ];

                    return response($_response, 200);
                }
                $request->request->add([
                    'password' => $request->input('login.password'),
                    'remember' => $request->input('login.remember', 0),
                ]);
                if ($this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);
                    $_seconds = $this->limiter()->availableIn(
                        $this->throttleKey($request)
                    );
                    $_response['commands'][] = [
                        'command' => 'val',
                        'options' => [
                            'target' => "#{$_form} input[type='password']",
                            'data'   => ''
                        ]
                    ];
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => '<div>' . trans('forms.messages.login.throttle', ['seconds' => $_seconds]) . '</div>',
                            'status' => 'danger'
                        ]
                    ];

                    return response($_response, 200);
                }
                if ($this->attemptLogin($request)) {
                    $_blocked_account = $request->user()->blocked == 1 ? FALSE : TRUE;
                    if ($_blocked_account == FALSE) {
                        Auth::logout();
                        if ($request->ajax()) {
                            $_response['commands'][] = [
                                'command' => 'val',
                                'options' => [
                                    'target' => "#{$_form} input[type='password']",
                                    'data'   => ''
                                ]
                            ];
                            $_response['commands'][] = [
                                'command' => 'UK_notification',
                                'options' => [
                                    'text'   => '<div>' . trans('forms.messages.login.account_locked') . '</div>',
                                    'status' => 'danger'
                                ]
                            ];

                            return response($_response, 200);
                        } else {
                            throw ValidationException::withMessages([
                                'email_or_name' => [
                                    strip_tags('<div>' . trans('forms.messages.login.account_locked') . '</div>')
                                ],
                            ]);
                        }
                    } else {
                        $_user = Auth::user();
                        $_rollback_url = $_user->redirectAfterSingIn($request->get('rollback', $this->redirectTo));
                        $_response['commands'][] = [
                            'command' => 'clearForm',
                            'options' => [
                                'target' => "#{$_form}"
                            ]
                        ];
                        $_response['commands'][] = [
                            'command' => 'redirect',
                            'options' => [
                                'url' => $_rollback_url
                            ]
                        ];
                        //                            $_response['commands'][] = [
                        //                                'command' => 'analyticsFbq',
                        //                                'options' => [
                        //                                    'event' => 'USER_LOGGED_ON'
                        //                                ]
                        //                            ];
                        //                            $_response['commands'][] = [
                        //                                'command' => 'analyticsGtag',
                        //                                'options' => [
                        //                                    'event'        => 'LOGGED_ON',
                        //                                    'category'     => 'USER',
                        //                                    'event_action' => 'COMPLETE',
                        //                                ]
                        //                            ];
                        return response($_response, 200);
                    }
                }
                $this->incrementLoginAttempts($request);
                $_response['commands'][] = [
                    'command' => 'val',
                    'options' => [
                        'target' => "#{$_form} input[type='password']",
                        'data'   => ''
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => '<div>' . trans('forms.messages.login.failed') . '</div>',
                        'status' => 'danger'
                    ]
                ];

                return response($_response, 200);
            }
        }

    }
