<?php

    namespace App\Models\User;

    use App\Models\Components\Comment;
    use App\Models\Pharm\PharmPharmacy;
    use App\Models\Shop\Order;
    use App\Notifications\ResetPasswordNotification;
    use App\Notifications\VerifyEmail;
    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Pagination\Paginator;
    use Spatie\Permission\Traits\HasRoles;
    use Watson\Rememberable\Rememberable;

    class User extends Authenticatable implements MustVerifyEmail
    {

        use Notifiable;
        use HasRoles;
        use Rememberable;

        protected $classIndex = 'user';
        protected $guarded = [];
        protected $guard_name = 'web';
        protected $hidden = [
            'password',
            'remember_token',
        ];
        protected $perPage = 50;

        public function getAllRolesAttribute()
        {
            $_roles = NULL;
            if ($_roles = $this->getRoleNames()) {
                $_roles = $_roles->map(function ($_role) {
                    return Role::where('name', $_role)
                        ->first();
                });
            }

            return $_roles;
        }

        public function getViewRoleAttribute()
        {
            $_view_roles = NULL;
            if ($_roles = $this->all_roles) {
                $_view_roles = $_roles->map(function ($_role) {
                    return _l($_role->display_name, 'oleus.roles.edit', ['p' => ['id' => $_role->id]]);
                })->toArray();
            }

            return $_view_roles ? implode(',', $_view_roles) : NULL;
        }

        public function getRoleAttribute()
        {
            if ($_roles = $this->all_roles) return $_roles->first();

            return NULL;
        }

        public function getActiveAttribute()
        {
            return is_null($this->email_verified_at) ? FALSE : TRUE;
        }

        public function getFullNameAttribute()
        {
            $_name = NULL;
            if ($this->_profile->surname) $_name[] = $this->_profile->surname;
            if ($this->_profile->name) $_name[] = $this->_profile->name;
            if ($this->_profile->patronymic) $_name[] = $this->_profile->patronymic;

            return $_name ? implode(' ', $_name) : $this->email;
        }

        public function setProfile($data)
        {
            $_save = new Profile;
            $_save->fill([
                'name'         => $data['register']['name'] ?? NULL,
                'surname'      => $data['register']['surname'] ?? NULL,
                'phone'        => $data['register']['phone'] ?? NULL,
                'subscription' => $data['register']['subscription'][1] ?? 0,
            ]);
            $this->_profile()->save($_save);
        }

        public function _profile()
        {
            return $this->hasOne(Profile::class)
                ->withDefault();
        }

        public function _orders()
        {
            return $this->hasMany(Order::class, 'user_id')
                ->with([
                    '_products',
                    '_attach_file'
                ]);
        }

        public function _pharmacy()
        {
            return $this->hasOne(PharmPharmacy::class, 'user_id')
                ->withDefault();
        }

        public function redirectAfterSingIn($redirectTo = NULL)
        {
            if ($this->can(['access_dashboard'])) $redirectTo = 'oleus';

            return $redirectTo;
        }

        public static function _authors()
        {
            $_all_users = self::permission([
                'nodes_create',
                'nodes_update'
            ])
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'email'
                ]);

            return $_all_users->map(function ($_user) {
                $_user->full_name = $_user->_profile->full_name;

                return $_user;
            });
        }

        public function _order_items($page = 1, $per_page = 50)
        {
            if ($page) {
                Paginator::currentPageResolver(function () use ($page) {
                    return $page;
                });
            }
            $_response = $this->_orders()
                ->orderByDesc('created_at');
            if ($page) {
                Paginator::currentPageResolver(function () use ($page) {
                    return $page;
                });
            }
            $_response = $_response->paginate($per_page);
            if ($page > 1 && $_response->isEmpty()) abort(404);
            if ($_response->isNotEmpty() && count($_response->items())) {
                if ($_response->isNotEmpty()) {
                    $_response->getCollection()->transform(function ($_order) {
                        if ($_order->_products->isNotEmpty()) {
                            $_order->_products->transform(function ($_product) {
                                $_product->price = view_price($_product->price, $_product->price);
                                $_product->amount = view_price($_product->amount, $_product->amount);

                                return $_product;
                            });
                            $_order->quantity_in = $_order->_products->count();
                            $_order->amount = view_price($_order->amount, $_order->amount);
                        }

                        return $_order;
                    });
                }
                if ($page > 1) wrap()->set('seo.robots', 'noindex, follow', TRUE);
                if ($_response->hasMorePages()) {
                    $_page_number = $page ? : 1;
                    $_page_number++;
                    $_current_url = wrap()->get('seo.url_alias');
                    $_current_url_query = wrap()->get('seo.url_query');
                    $_url = trim($_current_url, '/') . "/page-{$_page_number}";
                    $_next_page_link = _u($_url) . $_current_url_query;
                    wrap()->set('seo.link_next', $_next_page_link, TRUE);
                }
                wrap()->set('seo.page_number', $page, TRUE);
            }

            return $_response;
        }

        public function sendEmailVerificationNotification()
        {
            $this->notify(new VerifyEmail($this));
        }

        public function sendPasswordResetNotification($token)
        {
            $this->notify(new ResetPasswordNotification($token));
        }

        public static function show_login_form()
        {
            $_form_id = 'form-login-user';
            $_form_generate = form_generate([
                'id'                => $_form_id,
                'action'            => _r('login'),
                'button_send_class' => 'uk-button-success',
                'button_send_title' => trans('forms.buttons.login.submit'),
                'fields'            => [
                    field_render('login.email_or_name', [
                        'label'       => 'forms.fields.login.email_or_name',
                        'required'    => TRUE,
                        'placeholder' => TRUE,
                        'form_id'     => $_form_id,
                    ]),
                    field_render('login.password', [
                        'type'        => 'password',
                        'label'       => 'forms.fields.login.password',
                        'attributes'  => [
                            'autocomplete' => 'new-password',
                        ],
                        'placeholder' => TRUE,
                        'required'    => TRUE,
                        'form_id'     => $_form_id
                    ]),
                    field_render('login.remember', [
                        'type'    => 'checkbox',
                        'values'  => [
                            1 => 'forms.fields.login.remember'
                        ],
                        'form_id' => $_form_id
                    ])
                ]
            ]);

            return $_form_generate;
        }

        public static function show_register_form()
        {
            $_form_id = 'form-register-user';
            $_form_generate = form_generate([
                'id'                => $_form_id,
                'action'            => _r('register'),
                'button_send_class' => 'uk-button-success',
                'button_send_title' => trans('forms.buttons.register.submit'),
                'fields'            => [
                    field_render('register.name', [
                        'required'    => TRUE,
                        'form_id'     => $_form_id,
                        'label'       => trans('forms.fields.profile.name'),
                        'placeholder' => TRUE,
                    ]),
                    field_render('register.surname', [
                        'form_id'     => $_form_id,
                        'label'       => trans('forms.fields.profile.surname'),
                        'placeholder' => TRUE,
                    ]),
                    field_render('register.email', [
                        'form_id'     => $_form_id,
                        'label'       => trans('forms.fields.profile.email'),
                        'placeholder' => TRUE,
                        'required'    => TRUE,
                    ]),
                    field_render('register.phone', [
                        'form_id'     => $_form_id,
                        'label'       => trans('forms.fields.profile.phone'),
                        'placeholder' => TRUE,
                        'required'    => TRUE,
                    ]),
                    field_render('register.password', [
                        'label'       => 'forms.fields.profile.password',
                        'type'        => 'password_confirmation',
                        'attributes'  => [
                            'autocomplete' => 'new-password',
                        ],
                        'required'    => TRUE,
                        'placeholder' => TRUE,
                        'form_id'     => $_form_id
                    ]),
                    field_render('register.subscription', [
                        'type'    => 'checkbox',
                        'values'  => [
                            1 => 'forms.fields.profile.subscription'
                        ],
                        'form_id' => $_form_id
                    ])
                ]
            ]);

            return $_form_generate;
        }

        public static function show_verification_email_form()
        {
            $_form_id = 'form-login-user';
            $_form_generate = form_generate([
                'id'                => $_form_id,
                'action'            => _r('login'),
                'button_send_class' => 'uk-button-success',
                'button_send_title' => trans('forms.buttons.login.submit'),
                'fields'            => [
                    field_render('login.email_or_name', [
                        'label'       => 'forms.fields.login.email_or_name',
                        'required'    => TRUE,
                        'placeholder' => TRUE,
                        'form_id'     => $_form_id,
                    ]),
                    field_render('login.password', [
                        'type'        => 'password',
                        'label'       => 'forms.fields.login.password',
                        'attributes'  => [
                            'autocomplete' => 'new-password',
                        ],
                        'placeholder' => TRUE,
                        'required'    => TRUE,
                        'form_id'     => $_form_id
                    ]),
                    field_render('login.remember', [
                        'type'    => 'checkbox',
                        'values'  => [
                            1 => 'forms.fields.login.remember'
                        ],
                        'form_id' => $_form_id
                    ])
                ]
            ]);

            return $_form_generate;
        }

        public static function show_forgot_email_form()
        {
            $_form_id = 'form-forgot-email';
            $_form_generate = form_generate([
                'id'                => $_form_id,
                'action'            => _r('password.email'),
                'button_send_class' => 'uk-button-success',
                'button_send_title' => trans('forms.buttons.forgot_email.submit'),
                'fields'            => [
                    field_render('email', [
                        'required'    => TRUE,
                        'form_id'     => $_form_id,
                        'label'       => trans('forms.fields.profile.email'),
                        'placeholder' => TRUE,
                    ]),
                ]
            ]);

            return $_form_generate;
        }

    }
