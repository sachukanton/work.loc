<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Advantage;
    use App\Models\Components\AdvantageItems;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Validator;

    class AdvantageController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();

            $this->middleware([
                'permission:advantages_read'
            ]);
            $this->titles = [
                'index'     => 'Список',
                'create'    => 'Добавить',
                'edit'      => 'Редактировать "<strong>:title</strong>"',
                'translate' => 'Перевод на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->base_route = 'advantages';
            $this->permissions = [
                'read'   => 'advantages_read',
                'create' => 'advantages_create',
                'update' => 'advantages_update',
                'delete' => 'advantages_delete',
            ];
            $this->entity = new Advantage();
           
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        field_render('locale', [
                            'type'  => 'hidden',
                            'value' => config('app.default_locale'),
                        ]),
                        field_render('title', [
                            'label'      => 'Заголовок',
                            'value'      => $entity->getTranslation('title', $this->defaultLocale),
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('sub_title', [
                            'label' => 'Под заголовок',
                            'value' => $entity->getTranslation('sub_title', $this->defaultLocale),
                        ]),
                        field_render('body', [
                            'label'      => 'Содержимое',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->getTranslation('body', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 8,
                            ]
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('status', [
                            'type'     => 'checkbox',
                            'name'     => 'status',
                            'selected' => $entity->exists ? $entity->status : 1,
                            'values'   => [
                                1 => 'Опубликовано',
                            ]
                        ])
                    ]
                ],
            ];
            if ($entity->exists) {
                $_form->tabs[] = [
                    'title'   => 'Список',
                    'content' => [
                        'section' => view('backend.partials.advantage.items', [
                            'items'  => $entity->_items,
                            'entity' => $entity
                        ])->render()
                    ]
                ];
            }
            $_form->tabs[] = $this->__form_tab_display_style($entity, 'background');
            $_form->tabs[] = $this->__form_tab_display_rules($entity);

            return $_form;
        }

        protected function _form_translate($entity, $locale)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, [
                'translate' => $this->permissions['update']
            ]);
            $_form->use_multi_language = FALSE;
            $_form->tabs[] = [
                'title'   => 'Параметры перевода',
                'content' => [
                    field_render('locale', [
                        'type'  => 'hidden',
                        'value' => $locale
                    ]),
                    field_render('translate', [
                        'type'  => 'hidden',
                        'value' => 1
                    ]),
                    field_render('title', [
                        'label'    => 'Заголовок',
                        'value'    => $entity->getTranslation('title', $locale),
                        'required' => TRUE
                    ]),
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->getTranslation('sub_title', $locale)
                    ]),
                    field_render('body', [
                        'label'      => 'Содержимое',
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->getTranslation('body', $locale),
                        'attributes' => [
                            'rows' => 8,
                        ]
                    ]),
                ]
            ];

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Advantage::orderBy('title')
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
                    'data' => 'Заголовок',
                ],
                [
                    'class' => 'uk-width-small',
                    'data'  => '<span uk-icon="icon: apps">',
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
                        plural_string($_item->_items()->count(), 'нет блоков|блок|блока|блоков'),
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>'
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
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->validate($request, [
                'title' => 'required',
            ], [], [
                'title' => 'Заголовок'
            ]);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'hidden_title',
                'status',
                'style_id',
                'style_class',
                'background_fid',
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item = Advantage::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Advantage $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->validate($request, [
                'title' => 'required',
            ], [], [
                'title' => 'Заголовок'
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                    'hidden_title',
                    'status',
                    'style_id',
                    'style_class',
                    'background_fid',
                ]);
                $_save['background_fid'] = $_background_fid['id'] ?? NULL;
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_item->update($_save);
            }
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_update($request, $_item);
        }

        public function item(Request $request, Advantage $entity, $action, $id = NULL, $locale = NULL)
        {
            $commands = [];
            switch ($action) {
                case 'add':
                case 'edit':
                    $_item = $id ? AdvantageItems::find($id) : new AdvantageItems();
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.advantage.item_modal', compact('_item', 'entity', 'locale', 'action'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    break;
                case 'save':
                    $_default_locale = config('app.default_locale');
                    $_save = $request->input('item');
                    if ($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('advantage_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        "item.title.{$_default_locale}" => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        "item.title.{$_default_locale}" => 'Заголовок',
                    ]);
                    foreach ($validate_rules as $field => $rule) {
                        $commands['commands'][] = [
                            'command' => 'removeClass',
                            'options' => [
                                'target' => '#' . str_slug('form_field_' . str_replace('.', '_', $field), '-'),
                                'data'   => 'uk-form-danger'
                            ]
                        ];
                    }
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
                            $commands['commands'][] = [
                                'command' => 'addClass',
                                'options' => [
                                    'target' => '#' . str_slug('form_field_' . str_replace('.', '_', $field), '-'),
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
                        $_save['advantage_id'] = $entity->id;
                        $_item_id = $_save['id'];
                        unset($_save['id']);
                        $_save['icon_fid'] = $_icon['id'] ?? NULL;
                        $_save['status'] = (int)($_save['status'] ?? 0);
                        AdvantageItems::updateOrCreate([
                            'id' => $_item_id
                        ], $_save);
                        Session::forget([
                            'item.icon_fid'
                        ]);
                        $items = $entity->_items()->orderBy('sort')->get();
                        $_items_output = view('backend.partials.advantage.items_table', compact('items'))
                            ->render();
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-advantage-items',
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
                    AdvantageItems::find($id)
                        ->delete();
                    $items = $entity->_items()->orderBy('sort')->get();
                    if ($items->isNotEmpty()) {
                        $_items_output = view('backend.partials.advantage.items_table', compact('items'))
                            ->render();
                    } else {
                        $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                    }
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-advantage-items',
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

        public function save_sort(Request $request, Advantage $entity)
        {
            $_sorting = $request->all();
            $entity->_items->each(function ($_item) use ($_sorting) {
                $_item->sort = $_sorting[$_item->id] ?? 0;
                $_item->save();
            });
            $items = $entity->_items()->orderBy('sort')->get();
            $_items_output = view('backend.partials.advantage.items_table', compact('items'))
                ->render();
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#list-advantage-items',
                    'data'   => $_items_output
                ]
            ];

            return response($commands, 200);
        }

    }
