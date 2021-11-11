<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Slider;
    use App\Models\Components\SliderItems;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Validator;

    class SliderController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'  => 'Список слайд-шоу',
                'create' => 'Добавить слайд-шоу',
                'edit'   => 'Редактировать слайд-шоу "<strong>:title</strong>"',
                'delete' => '',
            ];
            $this->middleware([
                'permission:sliders_read'
            ]);
            $this->base_route = 'sliders';
            $this->permissions = [
                'read'   => 'sliders_read',
                'create' => 'sliders_create',
                'update' => 'sliders_update',
                'delete' => 'sliders_delete'
            ];
            $this->entity = new Slider();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->use_multi_language = FALSE;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_field_preset = NULL;
            if ($_presets = config('os_images')) {
                $_preset_values[] = '-- Выбрать --';
                foreach ($_presets as $_preset_key => $_preset_value) {
                    if (isset($_preset_value['w']) && isset($_preset_value['h'])) {
                        $_label = "{$_preset_value['w']}px * {$_preset_value['h']}px";
                    } elseif (isset($_preset_value['w'])) {
                        $_label = "{$_preset_value['w']}px * auto";
                    } elseif (isset($_preset_value['h'])) {
                        $_label = "auto * {$_preset_value['h']}px";
                    }
                    $_preset_values[$_preset_key] = $_label;
                }
                $_field_preset = field_render('preset', [
                    'label'    => 'Формат отображаения',
                    'type'     => 'select',
                    'selected' => $entity->preset,
                    'class'    => 'uk-select2',
                    'values'   => $_preset_values
                ]);
            }
            $_form->tabs[] = [
                'title'   => 'Основные параметры',
                'content' => [
                    field_render('locale', [
                        'type'  => 'hidden',
                        'value' => config('app.default_locale'),
                    ]),
                    field_render('title', [
                        'label'    => 'Название',
                        'value'    => $entity->title,
                        'required' => TRUE
                    ]),
                    $_field_preset,
                    field_render('options', [
                        'label'      => 'Дополнительные настройки',
                        'type'       => 'textarea',
                        'value'      => $entity->options,
                        'class'      => 'uk-textarea',
                        'attributes' => [
                            'rows' => 5
                        ],
                        'help'       => '<a href="https://getuikit.com/docs/slideshow#component-options" target="_blank">Дополнительные настройки для слайдшоу</a>'
                    ]),
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Навигация</span></h3>',
                    field_render('slidenav', [
                        'type'     => 'checkbox',
                        'selected' => $entity->slidenav,
                        'values'   => [
                            1 => 'Стрелки навигации'
                        ]
                    ]),
                    field_render('dotnav', [
                        'type'     => 'checkbox',
                        'selected' => $entity->dotnav,
                        'values'   => [
                            1 => 'Точки навигации'
                        ]
                    ]),
                    '<hr class="uk-divider-icon">',
                    field_render('status', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'Опубликовано'
                        ]
                    ])
                ]
            ];
            if ($entity->exists) {
                $_form->tabs[] = [
                    'title'   => 'Слайды',
                    'content' => [
                        'section' => view('backend.partials.sliders.items', [
                            'items'  => $entity->_items,
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
            $_query = Slider::orderByDesc('status')
                ->orderBy('title')
                ->select([
                    '*'
                ])
                ->with([
                    '_items'
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
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: collectionsphoto_library">',
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
                'title' => 'Заголовок'
            ]);
            $_save = $request->only([
                'title',
                'preset',
                'status',
                'style_id',
                'style_class',
                'options',
                'slidenav',
                'dotnav',
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['slidenav'] = (int)($_save['slidenav'] ?? 0);
            $_save['dotnav'] = (int)($_save['dotnav'] ?? 0);
            $_item = Slider::updateOrCreate([
                'id' => NULL
            ], $_save);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Slider $_item)
        {
            $this->validate($request, [
                'title' => 'required'
            ], [], [
                'title' => 'Заголовок'
            ]);
            $_save = $request->only([
                'title',
                'preset',
                'status',
                'style_id',
                'style_class',
                'options',
                'slidenav',
                'dotnav'
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['slidenav'] = (int)($_save['slidenav'] ?? 0);
            $_save['dotnav'] = (int)($_save['dotnav'] ?? 0);
            $_item->update($_save);

            return $this->__response_after_update($request, $_item);
        }

        public function item(Request $request, Slider $entity, $action, $id = NULL)
        {
            $commands = [];
            switch ($action) {
                case 'add':
                case 'edit':
                    $_item = $id ? SliderItems::find($id) : new SliderItems();
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.sliders.item_modal', compact('_item', 'entity'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    break;
                case 'save':
                    $_default_locale = config('app.default_locale');
                    $_save = $request->input('item');
                    if ($background = $_save['background_fid']) {
                        $_background = array_shift($background);
                        Session::flash('item.background_fid', json_encode([f_get($_background['id'])]));
                    }
                    $validate_rules = [
                        "item.title.{$_default_locale}" => 'required',
                        'item.background_fid'           => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        "item.title.{$_default_locale}" => 'Заголовок',
                        'item.background_fid'           => 'Фон слайда'
                    ]);
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#modal-slider-item-form input',
                            'data'   => 'uk-form-danger'
                        ]
                    ];
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
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
                        $_save['slider_id'] = $entity->id;
                        if (isset($_background)) $_save['background_fid'] = (int)$_background['id'];
                        $_item_id = $_save['id'];
                        unset($_save['id']);
                        $_save['hidden_title'] = (int)($_save['hidden_title'] ?? 0);
                        $_save['status'] = (int)($_save['status'] ?? 0);
                        SliderItems::updateOrCreate([
                            'id' => $_item_id
                        ], $_save);
                        Session::forget([
                            'item.background_fid'
                        ]);
                        $items = $entity->_items()->orderBy('sort')->get();
                        $_items_output = view('backend.partials.sliders.items_table', compact('items'))
                            ->render();
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-sliders-items',
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
                    SliderItems::find($id)
                        ->delete();
                    $slider_items = $entity->_items;
                    if ($slider_items->isNotEmpty()) {
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-sliders-items',
                                'data'   => view('backend.partials.sliders.items_table', ['items' => $slider_items])
                                    ->render()
                            ]
                        ];
                    } else {
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-sliders-items',
                                'data'   => '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>'
                            ]
                        ];
                    }
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'success',
                            'text'   => 'Элемент удален'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_modalClose'
                    ];
                    break;
            }
            update_last_modified_timestamp();

            return response($commands, 200);
        }

        public function save_sort(Request $request, Slider $entity)
        {
            $_sorting = $request->all();
            $entity->_items->each(function ($_item) use ($_sorting) {
                $_item->sort = $_sorting[$_item->id] ?? 0;
                $_item->save();
            });
            $items = $entity->_items()->orderBy('sort')->get();
            $_items_output = view('backend.partials.sliders.items_table', compact('items'))
                ->render();
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#list-sliders-items',
                    'data'   => $_items_output
                ]
            ];

            return response($commands, 200);
        }

    }
