<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Variable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class VariablesController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:variables_read'
            ]);
            $this->titles = [
                'index'     => 'Список переменных',
                'create'    => 'Добавить переменную',
                'edit'      => 'Редактировать переменную "<strong>:name</strong>"',
                'translate' => 'Перевод переменной на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->base_route = 'variables';
            $this->permissions = [
                'read'   => 'variables_read',
                'create' => 'variables_create',
                'update' => 'variables_update',
                'delete' => 'variables_delete',
            ];
            $this->entity = new Variable();
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
                        '<div class="uk-grid uk-child-width-1-2"><div>',
                        field_render('name', [
                            'label'      => 'Название переменной',
                            'value'      => $entity->name,
                            'attributes' => [
                                'autofocus' => TRUE,
                            ],
                            'help'       => 'Название переменной (используется только в панели для осозная, что это за переменная и, что она в себе хранит)',
                            'required'   => TRUE
                        ]),
                        '</div><div>',
                        field_render('key', [
                            'label'      => 'Машинное имя (ключ по которому она будет доступна)',
                            'value'      => $entity->key,
                            'attributes' => [
                                'readonly' => $entity->exists ? TRUE : FALSE
                            ],
                            'help'       => $entity->exists ? NULL : 'При заполнении можно использовать символы латиского алфавита и знак подчеркивания.',
                            'required'   => !$entity->exists ? TRUE : FALSE
                        ]),
                        '</div></div>',
                        field_render('value', [
                            'label'      => 'Значение переменной',
                            'type'       => 'textarea',
                            'class'      => 'uk-codeMirror',
                            'value'      => $entity->getTranslation('value', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 12,
                            ],
                            'help'       => 'Задайте значение переменной. Поле воспринимает код',
                            'required'   => TRUE
                        ]),
                        field_render('comment', [
                            'label'      => 'Комментарий',
                            'type'       => 'textarea',
                            'value'      => $entity->comment,
                            'attributes' => [
                                'rows' => 4,
                            ]
                        ]),
                        field_render('use_php', [
                            'type'     => 'checkbox',
                            'selected' => $entity->use_php,
                            'values'   => [
                                1 => 'Исполняемы код <span class="uk-text-bold">&lt;PHP&gt;</span>'
                            ]
                        ])
                    ]
                ],
                $this->__form_tab_display_rules($entity)
            ];

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
                    field_render('value', [
                        'label'      => 'Значение переменной',
                        'type'       => 'textarea',
                        'class'      => 'uk-codeMirror',
                        'value'      => $entity->getTranslation('value', $locale),
                        'attributes' => [
                            'rows' => 12,
                        ],
                        'help'       => 'Задайте значение переменной. Поле воспринимает код',
                        'required'   => TRUE
                    ]),
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
            $_query = Variable::when($_filter, function ($query) use ($_filter) {
                if ($_filter['name']) $query->where('name', 'like', "%{$_filter['name']}%");
            })
                ->orderBy('name')
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
                    'class' => 'uk-width-medium',
                    'data'  => 'KEY',
                ],
                [
                    'data' => 'Название переменной',
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
                        $_item->key,
                        $_item->name,
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
            $_filters[] = [
                'data' => field_render('name', [
                    'value'      => $_filter['name'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Название переменной'
                    ]
                ])
            ];
            $_items = $this->__items([
                'buttons'     => $_buttons,
                'headers'     => $_headers,
                'filters'     => $_filters,
                'use_filters' => $_filter ? TRUE : FALSE,
                'items'       => $_items,
                'pagination'  => $_query->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function store(Request $request)
        {
            if ($this->__can_permission('create') == FALSE) abort(403);
            $this->validate($request, [
                'key'   => 'sometimes|required|unique:variables|regex:/^[a-zA-Z0-9_-]+$/u',
                'name'  => 'sometimes|required',
                'value' => 'required',
            ], [], [
                'key'   => 'Машинное имя',
                'title' => 'Название переменной',
                'value' => 'Значение переменной'
            ]);
            $_save = $request->only([
                'key',
                'name',
                'value',
                'comment',
                'use_php',
            ]);
            $_save['use_php'] = (int)($_save['use_php'] ?? 0);
            $_item = Variable::updateOrCreate([
                'id' => NULL
            ], $_save);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Variable $_item)
        {
            if ($this->__can_permission('edit') == FALSE) abort(403);
            $request->offsetUnset('key');
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $this->validate($request, [
                    'value' => 'required',
                ], [], [
                    'value' => 'Значение переменной'
                ]);
                $_save = $request->only([
                    'value'
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $this->validate($request, [
                    'name'  => 'sometimes|required',
                    'value' => 'required',
                ], [], [
                    'name'  => 'Название переменной',
                    'value' => 'Значение переменной'
                ]);
                $_save = $request->only([
                    'name',
                    'value',
                    'comment',
                    'use_php',
                ]);
                $_save['use_php'] = (int)($_save['use_php'] ?? 0);
                $_item->update($_save);
            }

            return $this->__response_after_update($request, $_item);
        }

        public function translate(Request $request, Variable $_item, $locale)
        {
            $_locale = config("laravellocalization.supportedLocales.{$locale}");
            $_wrap = $this->render([
                'seo.title' => str_replace(':locale', $_locale['name'], $this->titles['translate'])
            ]);
            $_form = $this->_form_translate($_item, $locale);

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }

    }
