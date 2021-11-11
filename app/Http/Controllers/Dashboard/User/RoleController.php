<?php

    namespace App\Http\Controllers\Dashboard\User;

    use App\Library\BaseController;
    use App\Library\Dashboard;
    use App\Models\User\Permission;
    use App\Models\User\Role;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class RoleController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список ролей пользователей',
                'create'    => 'Добавить роль',
                'edit'      => 'Редактировать роль "<strong>:name</strong>"',
                'translate' => '',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:roles_read'
            ]);
            $this->permissions = [
                'read'   => 'roles_read',
                'create' => 'roles_create',
                'update' => 'roles_update',
                'delete' => 'roles_delete',
            ];
            $this->base_route = 'roles';
            $this->entity = new Role();
        }

        public function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->use_multi_language = FALSE;
            $_form->permission = array_merge($_form->permission, [
                'read'   => 'roles_read',
                'create' => 'roles_create',
                'update' => 'roles_update',
                'delete' => 'roles_delete',
            ]);
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        field_render('name', [
                            'label'      => 'Машинное имя',
                            'value'      => $entity->name,
                            'required'   => TRUE,
                            'attributes' => $entity->exists ? ['readonly' => TRUE] : ['autofocus' => TRUE],
                            'help'       => !$entity->exists ? 'При заполнении можно использовать символы латиского алфавита и знак подчеркивания.' : NULL
                        ]),
                        field_render('display_name', [
                            'label'    => 'Название',
                            'value'    => $entity->display_name,
                            'required' => TRUE,
                        ])
                    ]
                ]
            ];
            if ($entity->exists) {
                $_permissions = Permission::orderBy('description')
                    ->orderBy('name')
                    ->pluck('display_name', 'name')
                    ->toArray();
                $_form->tabs[] = [
                    'title'   => 'Доступные права',
                    'content' => [
                        field_render('permissions', [
                            'label'    => 'Права',
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->permissions->pluck('name')->toArray() : NULL,
                            'values'   => $_permissions,
                        ])
                    ],
                ];
            }

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Role::with('permissions')
                ->select([
                    '*'
                ])
                ->paginate($this->entity->getPerPage(), ['id']);
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_role) use ($_user) {
                    $_response = [
                        'id'           => "<div class='uk-text-center uk-text-bold'>{$_role->id}</div>",
                        'name'         => $_role->name,
                        'display_name' => $_role->display_name,
                        'permissions'  => "<div class='uk-text-center uk-text-bold uk-teal-text text-darken-2'>{$_role->permissions->count()}</div>",
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_response[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_role->id
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
            $_buttons = [];
            $_headers = [
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => 'ID',
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => 'Машинное имя',
                ],
                [
                    'data' => 'Название название',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: code">',
                ]
            ];
            if ($_user->hasPermissionTo($this->permissions['update'])) {
                $_headers[] = [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: createmode_editedit">',
                ];
            }
            if ($_user->hasPermissionTo($this->permissions['create'])) {
                $_buttons[] = _l('Добавить', "oleus.{$this->base_route}.create", [
                    'attributes' => [
                        'class' => 'uk-button uk-button-success'
                    ]
                ]);
            }
            $_items = $this->__items([
                'buttons'    => $_buttons,
                'headers'    => $_headers,
                'items'      => $_items,
                'pagination' => $_query->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'name'         => 'required|alpha_dash|unique:roles|max:191',
                'display_name' => 'required|max:191',
            ], [], [
                'name'         => 'Машинное имя',
                'display_name' => 'Название'
            ]);
            $_save = $request->only([
                'name',
                'display_name',
                'bonus_percent',
            ]);
            $_save['guard_name'] = Role::$defaultGuardName;
            $_item = Role::updateOrCreate([
                'id' => NULL
            ], $_save);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Role $_item)
        {
            $this->validate($request, [
                'permissions'  => 'required|array',
                'display_name' => 'required|max:191',
            ], [], [
                ''
            ]);
            $_save = $request->only([
                'display_name',
                'bonus_percent',
            ]);
            $_item->update($_save);

            return $this->__response_after_update($request, $_item);
        }

    }
