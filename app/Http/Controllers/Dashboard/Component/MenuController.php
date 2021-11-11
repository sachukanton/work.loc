<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Menu;
    use App\Models\Components\MenuItems;
    use App\Models\Seo\UrlAlias;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Validator;

    class MenuController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список меню',
                'create'    => 'Добавить меню',
                'edit'      => 'Редактировать меню "<strong>:title</strong>"',
                'translate' => '',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:menus_read'
            ]);
            $this->base_route = 'menus';
            $this->permissions = [
                'read'   => 'menus_read',
                'create' => 'menus_create',
                'update' => 'menus_update',
                'delete' => 'menus_delete',
            ];
            $this->entity = new Menu();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->use_multi_language = FALSE;
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_form->tabs[] = [
                'title'   => 'Основные параметры',
                'content' => [
                    field_render('title', [
                        'label'    => 'Название меню',
                        'value'    => $entity->title,
                        'required' => TRUE
                    ]),
                    '<hr class="uk-divider-icon">',
                    field_render('status', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'Опубликовано',
                        ]
                    ])
                ]
            ];
            if ($entity->exists) {
                $_form->tabs[] = [
                    'title'   => 'Пункты меню',
                    'content' => [
                        view('backend.partials.menu.items', [
                            'items'  => $entity->_items()
                                ->whereNull('parent_id')
                                ->get(),
                            'entity' => $entity
                        ])->render()
                    ]
                ];
            }
            $_form->tabs[] = $this->__form_tab_display_style($entity);
            $_form->tabs[] = $this->__form_tab_display_rules($entity);

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Menu::with([
                '_items'
            ])
                ->select([
                    '*'
                ])
                ->paginate($this->entity->getPerPage(), ['id']);
            $_buttons = [];
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
                    'data' => 'Название меню',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: menu"></span>',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: laptop_windows">',
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
                        $_item->title,
                        (string)$_item->_items->count(),
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
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
                'title' => 'required'
            ], [], [
                'title' => 'Название меню'
            ]);
            $_save = $request->only([
                'title',
                'status',
                'style_id',
                'style_class',
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item = Menu::updateOrCreate([
                'id' => NULL
            ], $_save);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Menu $_item)
        {
            $this->validate($request, [
                'title' => 'required',
            ], [], [
                'title' => 'Название меню'
            ]);
            $_save = $request->only([
                'title',
                'status',
                'style_id',
                'style_class',
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item->update($_save);

            return $this->__response_after_update($request, $_item);

        }

        public function item(Request $request, Menu $entity, $action, $id = NULL)
        {
            $commands = [];
            $_parents = MenuItems::where('menu_id', $entity->id)
                ->when($id, function ($_query) use ($id) {
                    $_query->where('id', '<>', $id);
                })
                ->orderBy('sort')
                ->pluck('title', 'id');
            if ($_parents->isNotEmpty()) $_parents->prepend('-- Выбрать --', 0);
            switch ($action) {
                case 'add':
                case 'edit':
                    $_item = $id ? MenuItems::find($id) : new MenuItems();
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.menu.item_modal', compact('_item', 'entity', '_parents'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'easyAutocomplete'
                    ];
                    break;
                case 'save':
                    $_default_locale = config('app.default_locale');
                    $_save = $request->input('item');
                    if ($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        "item.title.{$_default_locale}" => 'required',
                        'item.link.name'                => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        "item.title.{$_default_locale}" => 'Название пункта меню',
                        'item.link.name'                => 'Ссылка на материал'
                    ]);
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#modal-menu-item-form input',
                            'data'   => 'uk-form-danger'
                        ]
                    ];
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
                            $field = $field == 'item.link.name' ? 'item.link' : $field;
                            $commands['commands'][] = [
                                'command' => 'addClass',
                                'options' => [
                                    'target' => '#' . generate_field_id($field),
                                    'data'   => 'uk-form-danger'
                                ]
                            ];
                        }
                        $commands['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'status' => 'danger',
                                'text'   => 'Ошибка! Запрос не прошел проверку'
                            ]
                        ];
                    } else {
                        $_save['menu_id'] = $entity->id;
                        if (isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        $_save['data'] = serialize($_save['data']);
                        $_save['parent_id'] = isset($_save['parent_id']) && $_save['parent_id'] ? $_save['parent_id'] : NULL;
                        $_item_id = $_save['id'];
                        unset($_save['id']);
                        $_alias_id = NULL;
                        $_link = NULL;
                        if (in_array($_save['link']['name'], MenuItems::DEFAULT_ITEMS)) {
                            $_link = $_save['link']['name'];
                        } elseif ($_save['link']['value']) {
                            $_alias_id = $_save['link']['value'];
                        } else {
                            $_link = $_save['link']['name'];
                        }
                        $_save['status'] = (int)($_save['status'] ?? 0);
                        if (!is_null($_alias_id) || !is_null($_link)) {
                            $_save['alias_id'] = $_alias_id;
                            $_save['link'] = $_link;
                            MenuItems::updateOrCreate([
                                'id' => $_item_id
                            ], $_save);
                        }
                        Session::forget([
                            'item.icon_fid',
                        ]);
                        $items = $entity->_items()->orderBy('sort')->whereNull('parent_id')->get();
                        $_items_output = view('backend.partials.menu.items_table', compact('items'))
                            ->render();
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-menu-items',
                                'data'   => $_items_output
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => 'Элемент сохранен',
                                'status' => 'success',
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'UK_modalClose',
                            'options' => []
                        ];
                    }
                    break;
                case 'destroy':
                    MenuItems::find($id)
                        ->delete();
                    $items = $entity->_items()->orderBy('sort')->whereNull('parent_id')->get();
                    if ($items->isNotEmpty()) {
                        $_items_output = view('backend.partials.menu.items_table', compact('items'))
                            ->render();
                    } else {
                        $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                    }
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-menu-items',
                            'data'   => $_items_output
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => 'Элемент удален',
                            'status' => 'success',
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_modalClose',
                        'options' => []
                    ];
                    break;
            }
            update_last_modified_timestamp();

            return response($commands, 200);
        }

        public function search_link(Request $request)
        {
            $_items = [];
            if ($_search = $request->input('search')) {
                $_str = substr(strstr($_search, '::'), 2, strlen($_search));
                if($_str) $_search = $_str;
                $_url = new UrlAlias();
                $_items = $_url->_items_for_menu($_search);
            }

            return response($_items, 200);
        }

        public function save_sort(Request $request, Menu $entity)
        {
            $_sorting = $request->all();
            $entity->_items->each(function ($_item) use ($_sorting) {
                $_item->sort = $_sorting[$_item->id] ?? 0;
                $_item->save();
            });
            $items = $entity->_items()->orderBy('sort')->whereNull('parent_id')->get();
            $_items_output = view('backend.partials.menu.items_table', compact('items'))
                ->render();
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#list-menu-items',
                    'data'   => $_items_output
                ]
            ];

            return response($commands, 200);
        }

    }
