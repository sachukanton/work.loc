<?php

    namespace App\Http\Controllers\Dashboard\User;

    use App\Exports\UsersExport;
    use App\Library\BaseController;
    use App\Models\User\Role;
    use App\Models\User\User;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Maatwebsite\Excel\Facades\Excel;

    class UserController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:users_read'
            ]);
            $this->titles = [
                'index'  => 'Список пользователей',
                'create' => 'Создать пользователя',
                'edit'   => 'Редактировать пользователя "<strong>:name</strong>"',
                'delete' => '',
            ];
            $this->base_route = 'users';
            $this->permissions = [
                'read'   => 'users_read',
                'create' => 'users_create',
                'update' => 'users_update',
                'delete' => 'users_delete',
            ];
            $this->entity = new User();
        }

        protected function _form($entity)
        {
            $this->__filter();
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_user = wrap()->get('user');
            $_roles = Role::all();
            $_form = $this->__form();
            $_form->use_multi_language = FALSE;
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, [
                'read'   => 'users_read',
                'create' => 'users_create',
                'update' => 'users_update',
                'delete' => 'users_delete',
            ]);
            $_field_roles = NULL;
            $_field_blocked = NULL;
            if ($_user->can('roles_assignment')) {
                $_roles = $_roles->filter(function ($_role) use ($_user) {
                    if (!$_user->hasRole('super_admin')) {
                        return $_role->name != 'super_admin';
                    }
                    if (!$_user->hasRole('admin') && !$_user->hasRole('super_admin')) {
                        return !in_array($_role->name, [
                            'super_admin',
                            'admin'
                        ]);
                    }

                    return TRUE;
                })->pluck('display_name', 'name')->toArray();
                $_field_roles = field_render('role', [
                    'type'     => 'select',
                    'label'    => 'Роль пользователя',
                    'value'    => $entity->exists ? $entity->getRoleNames()->first() : 'user',
                    'values'   => $_roles,
                    'class'    => 'uk-select2',
                    'required' => TRUE
                ]);
            } else {
                $_field_roles = field_render('role', [
                    'type'  => 'hidden',
                    'value' => $entity->exists ? $entity->getRoleNames()->first() : 'user',
                ]);
            }
            if ($entity->exists) {
                if ($_user->can('users_create', 'users_update') && ($_user->hasRole('super_admin') || $_user->hasRole('admin'))) {
                    $_field_blocked .= field_render('blocked', [
                        'type'     => 'checkbox',
                        'label'    => 'Заблокирован',
                        'selected' => $entity->blocked,
                        'values'   => [
                            1 => 'Заблокировать аккаунт'
                        ],
                        'help'     => 'Заблокирует доступ к аккаунту пользователя. Аккаунт при этом не удаляется.',
                    ]);
                }
            }
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        '<div class="uk-grid uk-child-width-1-2"><div>',
                        field_render('email', [
                            'type'       => 'email',
                            'label'      => 'E-mail',
                            'value'      => $entity->email,
                            'attributes' => ['autofocus' => TRUE],
                            'required'   => TRUE,
                        ]),
                        '</div><div>',
                        field_render('name', [
                            'label'    => 'Логин',
                            'value'    => $entity->name,
                            'required' => TRUE,
                        ]),
                        '</div></div><div class="uk-grid uk-child-width-1-2"><div>',
                        field_render('password', [
                            'type'       => 'password',
                            'label'      => 'Пароль',
                            'value'      => NULL,
                            'attributes' => [
                                'autocomplete' => 'new-password'
                            ],
                            'required'   => $entity->exists ? FALSE : TRUE,
                        ]),
                        '</div><div>',
                        $_field_roles,
                        '</div></div>',
                        '<hr class="uk-divider-icon">',
                        field_render('active', [
                            'type'     => 'checkbox',
                            'label'    => 'Активирован',
                            'values'   => [
                                1 => 'Активировать аккаунт'
                            ],
                            'selected' => $entity->active,
                            'help'     => 'Активация аккаунта после подтвержения email.',
                        ]),
                        $_field_blocked
                    ],
                ],
                [
                    'title'   => 'Профиль',
                    'content' => [
                        '<div uk-grid class="uk-grid-small uk-form-column">',
                        '<div class="uk-width-1-3">',
                        field_render('profile.avatar_fid', [
                            'type'   => 'file',
                            'label'  => 'Аватарка',
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'view'   => 'avatar',
                            'values' => $entity->exists && $entity->_profile->_avatar ? [$entity->_profile->_avatar] : NULL,
                        ]),
                        '</div><div class="uk-width-2-3">',
                        field_render('profile.name', [
                            'label' => 'Имя',
                            'value' => $entity->_profile->name ?? NULL
                        ]),
                        field_render('profile.surname', [
                            'label' => 'Фамилия',
                            'value' => $entity->_profile->surname ?? NULL
                        ]),
                        field_render('profile.patronymic', [
                            'label' => 'Отчество',
                            'value' => $entity->_profile->patronymic ?? NULL
                        ]),
                        field_render('profile.phone', [
                            'label' => 'Номер телефона',
                            'value' => $entity->_profile->phone ?? NULL,
                            'class' => 'uk-phone-mask'
                        ]),
                        field_render('profile.sex', [
                            'type'   => 'radio',
                            'label'  => 'Пол',
                            'value'  => $entity->_profile->sex ?? 'male',
                            'values' => [
                                'male'   => 'Мужчина',
                                'female' => 'Женщина',
                            ]
                        ]),
                        field_render('profile.birthday', [
                            'label'      => 'Дата рождения',
                            'value'      => $entity->exists && $entity->_profile->birthday ? $entity->_profile->birthday->format('d.m.Y') : NULL,
                            'class'      => 'uk-datepicker',
                            'attributes' => [
                                'data-position' => 'top left'
                            ]
                        ]),
                        field_render('profile.comment', [
                            'type'       => 'textarea',
                            'label'      => 'Коментарий',
                            'value'      => $entity->_profile->comment ?? NULL,
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        '</div></div>'
                    ]
                ]
            ];

            return $_form;
        }

        protected function _items($_wrap)
        {
            $this->__filter();
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_filter = $this->filter;
            $_user = Auth::user();
            $_items = collect([]);
            $_query = User::from('users as u')
                ->leftJoin('users_profile as up', 'up.user_id', '=', 'u.id')
                ->orderByDesc('u.created_at')
                ->when($_filter, function ($query) use ($_filter) {
                    if ($_filter['email']) $query->where('u.email', 'like', "%{$_filter['email']}%");
                    if (!is_null($_filter['blocked'])) $query->where('u.blocked', $_filter['blocked']);
                    if ($_filter['phone']) {
                        $query->where('up.phone', 'like', "%{$_filter['phone']}%");
                    }
                })
                ->with([
                    '_profile'
                ])
                ->select([
                    'u.*'
                ])
                ->paginate($this->entity->getPerPage(), ['u.id']);
            $_buttons = [];
            if ($_query->isNotEmpty() && $_user->hasPermissionTo('users_export_data')) {
                //                $_buttons[] = _l('Экспорт', 'oleus.users.export', [
                //                    'attributes' => [
                //                        'class' => ['uk-button uk-button-warning uk-text-uppercase']
                //                    ]
                //                ]);
            }
            if ($_user->hasPermissionTo($this->permissions['create'])) {
                $_buttons[] = _l('Добавить', "oleus.{$this->base_route}.create", [
                    'attributes' => [
                        'class' => 'uk-button uk-button-success uk-text-uppercase'
                    ]
                ]);
            }
            $_headers = [
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => 'ID',
                ],
                [
                    'data' => 'ФИО',
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => 'E-mail',
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => 'Номер телефона',
                ],
                [
                    'class' => 'uk-width-small',
                    'data'  => 'Роль',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: mail_outline">',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: block">',
                ]
            ];
            if ($_user->hasPermissionTo($this->permissions['update'])) {
                $_headers[] = [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: createmode_editedit">',
                ];
            }
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_item) use ($_user) {
                    $_response = [
                        "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                        $_item->full_name,
                        $_item->email,
                        $_item->_profile->phone ?? '-',
                        $_item->view_role,
                        $_item->active ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                        !$_item->blocked ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_response[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->id
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-button-small',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_response;
                });
            }
            $_filters = [
                [
                    'class' => 'uk-width-large',
                    'data'  => field_render('email', [
                        'value'      => $_filter['email'] ?? NULL,
                        'attributes' => [
                            'placeholder' => 'E-mail'
                        ]
                    ])
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => field_render('phone', [
                        'value'      => $_filter['phone'] ?? NULL,
                        'attributes' => [
                            'placeholder' => 'Номер телефона'
                        ]
                    ])
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => field_render('blocked', [
                        'type'   => 'select',
                        'value'  => $_filter['blocked'] ?? NULL,
                        'values' => [
                            '' => 'Статус пользователя',
                            0  => 'Активен',
                            1  => 'Заблокирован',
                        ],
                        'class'  => 'uk-select2',
                    ])
                ]
            ];
            $_items = $this->__items([
                'filters'     => $_filters,
                'use_filters' => $_filter ? TRUE : FALSE,
                'buttons'     => $_buttons,
                'headers'     => $_headers,
                'items'       => $_items,
                'pagination'  => $_query->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function store(Request $request)
        {
            if ($avatar_fid = $request->input('profile.avatar_fid')) {
                $_avatar_fid = array_shift($avatar_fid);
                Session::flash('profile.avatar_fid', json_encode([f_get($_avatar_fid['id'])]));
            }
            $this->validate($request, [
                'name'     => 'required|alpha_dash|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6',
                'role'     => 'required',
            ], [], [
                'name'     => 'Логин',
                'email'    => 'E-mail',
                'password' => 'Пароль',
            ]);
            $_save = $request->only([
                'name',
                'email',
                'password',
                'active',
            ]);
            $_save['password'] = bcrypt($_save['password']);
            if ($_save['active']) $_save['email_verified_at'] = Carbon::now();
            unset($_save['active']);
            $_item = User::updateOrCreate([
                'id' => NULL
            ], $_save);
            $_item->syncRoles($request->input('role'));
            Session::forget([
                'profile.avatar_fid',
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, User $_item)
        {
            if ($avatar_fid = $request->input('profile.avatar_fid')) {
                $_avatar_fid = array_shift($avatar_fid);
                Session::flash('profile.avatar_fid', json_encode([f_get($_avatar_fid['id'])]));
            }
            $this->validate($request, [
                'name'     => 'required|alpha_dash|max:255',
                'email'    => 'required|email|max:255',
                'role'     => 'required',
            ]);
            $_save = $request->only([
                'name',
                'email',
                'active',
                'blocked',
            ]);
            if ($_password = $request->input('password')) $_save['password'] = bcrypt($_password);
            $_save['email_verified_at'] = isset($_save['active']) && $_save['active'] ? Carbon::now() : NULL;
            $_save['blocked'] = (int)($_save['blocked'] ?? 0);
            unset($_save['active']);
            $_item->update($_save);
            $_old_role = $_item->role;
            $_new_role = $request->input('role');
            if (($_old_role && $_old_role->name != $_new_role) || (!$_old_role && $_new_role)) {
                if ($_old_role) $_item->removeRole($_old_role);
                $_item->syncRoles($_new_role);
            }
            Session::forget([
                'profile.avatar_fid',
            ]);
            if ($request->input('save_close')) {
                return redirect()
                    ->route("oleus.{$this->base_route}")
                    ->with('notice', [
                        'message' => $this->notifications['updated'],
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route("oleus.{$this->base_route}.edit", ['id' => $_item->id])
                ->with('notice', [
                    'message' => $this->notifications['updated'],
                    'status'  => 'success'
                ]);
        }

        public function export(Request $request)
        {
            return Excel::download(new UsersExport(), 'export_users.xlsx');
        }

    }
