<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\Structure\Page;
    use App\Models\User\User;
    use Illuminate\Auth\Events\Registered;
    use Illuminate\Foundation\Auth\RegistersUsers;
    use Illuminate\Http\Request;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class RegisterController extends Controller
    {

        use RegistersUsers,
            Notifiable;

        protected $redirectTo = '/';

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        protected function create($data)
        {
            $_user = User::create([
                'name'     => $data['register']['login'],
                'email'    => $data['register']['email'],
                'password' => Hash::make($data['register']['password']),
            ]);
            $_user->setProfile($data);
            $_user->syncRoles('user');

            return $_user;
        }

        public function showRegistrationForm()
        {
            $_wrap = wrap()->get();
            $_template = [
                "frontend.{$this->deviceTemplate}.user.register",
                'frontend.default.user.register'
            ];
            $_item = new Page();
            $_item->setWrap([
                'seo.robots'      => 'noindex, nofollow',
                'seo.title'       => trans('pages.titles.register'),
                'page.title'      => trans('pages.titles.register'),
                'page.style_id'   => 'page-register-form',
                'seo.url_alias'   => 'register',
                'page.breadcrumb' => collect([
                    [
                        'name'     => trans('pages.titles.home'),
                        'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                        'position' => 1
                    ],
                    [
                        'name'     => trans('pages.titles.register'),
                        'url'      => NULL,
                        'position' => 2
                    ]
                ]),
                'alias'           => 'register'
            ]);
            $_item->registerFormOutput = User::show_register_form();
            $_wrap = wrap()->render();

            return View::first($_template, compact('_item', '_wrap'));
        }

        public function register(Request $request)
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
                    'register.email'    => 'required|string|email|max:255|unique:users,email',
                    'register.password' => 'required|string|min:8|confirmed',
                    'register.name'     => 'required|string',
                    'register.phone'    => 'required|string|phoneNumber|phoneOperatorCode',
                    'captcha'           => 'required|reCaptchaV3',
                ], [], [
                    'register.email'    => trans('forms.fields.profile.email'),
                    'register.password' => trans('forms.fields.profile.password'),
                    'register.name'     => trans('forms.fields.profile.name'),
                    'register.phone'    => trans('forms.fields.profile.phone'),
                    'captcha'           => trans('forms.fields.captcha'),
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
                        if ($_field == 'password') {
                            $_response['commands'][] = [
                                'command' => 'addClass',
                                'options' => [
                                    'target' => '#' . generate_field_id($_field, $_form) . '-confirmation',
                                    'data'   => 'uk-form-danger'
                                ]
                            ];
                        }
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
                } else {
                    $_register = $request->get('register');
                    $_name = explode('@', $request->input('register.email'));
                    $_register['login'] = str_slug($_name[0], '_');
                    $request->request->set('register', $_register);
                    event(new Registered($_user = $this->create($request->all())));
                    $this->guard()->login($_user);
                    $this->registered($request, $_user);
                    spy("На сайте зарегистрирован новый пользователь <a href='/oleus/users/{$_user->id}/edit'>{$_user->email}</a>.", 'success');
                    $_response['commands'][] = [
                        'command' => 'clearForm',
                        'options' => [
                            'target' => "#{$_form}"
                        ]
                    ];
                    //                    $_response['commands'][] = [
                    //                        'command' => 'analyticsFbq',
                    //                        'options' => [
                    //                            'event' => 'USER_HAS_REGISTERED'
                    //                        ]
                    //                    ];
                    //                    $_response['commands'][] = [
                    //                        'command' => 'analyticsGtag',
                    //                        'options' => [
                    //                            'event'        => 'REGISTERED',
                    //                            'category'     => 'USER',
                    //                            'event_action' => 'COMPLETE',
                    //                        ]
                    //                    ];
                    $_response['commands'][] = [
                        'command' => 'redirect',
                        'options' => [
                            'url'  => '/email/verify',
                            'time' => 500
                        ]
                    ];
                }

                return response($_response, 200);
            }
        }

    }
