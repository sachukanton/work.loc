<?php

    namespace App\Http\Controllers\Dashboard\Structure;

    use App\Library\BaseController;
    use App\Models\Structure\Faq;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class FaqController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список вопросов',
                'create'    => 'Добавить вопрос',
                'edit'      => 'Редактировать вопрос "<strong>:question</strong>"',
                'translate' => 'Перевод вопроса на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:faqs_read'
            ]);
            $this->base_route = 'faqs';
            $this->permissions = [
                'read'   => 'faqs_read',
                'create' => 'faqs_create',
                'update' => 'faqs_update',
                'delete' => 'faqs_delete'
            ];
            $this->entity = new Faq();
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
                        field_render('question', [
                            'label'      => 'Вопрос',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->getTranslation('question', $this->defaultLocale),
                            'attributes' => [
                                'rows'      => 8,
                                'autofocus' => TRUE,
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('answer', [
                            'label'      => 'Ответ',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->getTranslation('answer', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 8,
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('sort', [
                            'type'  => 'number',
                            'label' => 'Порядок сортировки',
                            'value' => $entity->exists ? $entity->sort : 0,

                        ]),
                        field_render('visible_on_block', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->visible_on_block : 1,
                            'values'   => [
                                1 => 'Вывести в блок'
                            ]
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->status : 1,
                            'values'   => [
                                1 => 'Опубликовано'
                            ]
                        ]),
                    ],
                ],
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
                    field_render('question', [
                        'label'      => 'Вопрос',
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->getTranslation('question', $locale),
                        'attributes' => [
                            'rows'      => 8,
                            'autofocus' => TRUE,
                        ],
                        'required'   => TRUE
                    ]),
                    field_render('answer', [
                        'label'      => 'Ответ',
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->getTranslation('answer', $locale),
                        'attributes' => [
                            'rows' => 8,
                        ],
                        'required'   => TRUE
                    ]),
                ]
            ];

            return $_form;
        }

        protected function _items($_wrap)
        {
            $this->__filter();
            $_filter = $this->filter;
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_filters = [];
            $_user = Auth::user();
            $_items = collect([]);
            $_faqs = Faq::when($_filter, function ($query) use ($_filter) {
                if ($_filter['question']) $query->where('question', 'like', "%{$_filter['question']}%");
            })
                ->orderByDesc('status')
                ->orderBy('sort')
                ->orderBy('question')
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
                    'data' => 'Вопрос',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: chrome_reader_mode">',
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
            if ($_faqs->isNotEmpty()) {
                $_items = $_faqs->map(function ($_item) use ($_user) {
                    $_table_row = [
                        "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                        str_limit(strip_tags($_item->getTranslation('question', $this->defaultLocale))),
                        $_item->show_in_block ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_table_row[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->id
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-button-small',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_table_row;
                });
            }
            $_filters[] = [
                'data' => field_render('question', [
                    'value'      => $_filter['question'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Вопрос'
                    ]
                ])
            ];
            $_items = $this->__items([
                'buttons'     => $_buttons,
                'headers'     => $_headers,
                'filters'     => $_filters,
                'use_filters' => $_filter ? TRUE : FALSE,
                'items'       => $_items,
                'pagination'  => $_faqs->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'question' => 'required',
                'answer'   => 'required',
            ], [], [
                'question' => 'Вопрос',
                'answer'   => 'Ответ',
            ]);
            $_save = $request->only([
                'question',
                'answer',
                'status',
                'sort',
                'visible_on_block',
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['visible_on_block'] = (int)($_save['visible_on_block'] ?? 0);
            $_item = Faq::updateOrCreate([
                'id' => NULL
            ], $_save);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Faq $_item)
        {
            $this->validate($request, [
                'question' => 'required',
                'answer'   => 'required',
            ], [], [
                'question' => 'Вопрос',
                'answer'   => 'Ответ',
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'question',
                    'answer',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $_save = $request->only([
                    'question',
                    'answer',
                    'status',
                    'sort',
                    'visible_on_block',
                ]);
                app()->setLocale($_locale);
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_save['visible_on_block'] = (int)($_save['visible_on_block'] ?? 0);
                $_item->update($_save);
            }

            return $this->__response_after_update($request, $_item);
        }

    }
