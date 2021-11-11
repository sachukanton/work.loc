<?php

    namespace App\Http\Controllers\Dashboard\Structure;

    use App\Library\BaseController;
    use App\Library\Dashboard;
    use App\Models\Structure\Tag;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class TagController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список страниц тегов',
                'create'    => 'Добавить страницу',
                'edit'      => 'Редактировать страницу тега "<strong>:title</strong>"',
                'translate' => 'Перевод страницы на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:tags_read'
            ]);
            $this->base_route = 'tags';
            $this->permissions = [
                'read'   => 'tags_read',
                'create' => 'tags_create',
                'update' => 'tags_update',
                'delete' => 'tags_delete',
            ];
            $this->entity = new Tag();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->seo = TRUE;
            $_field_parent = NULL;
            $_parents = $this->entity::tree_parents($entity->id);
            if ($_parents->isNotEmpty()) {
                $_parents = $_parents->map(function ($_item) {
                    return $_item['title_option'];
                });
                if ($_parents->isNotEmpty()) $_parents->prepend('-- Выбрать --', '');
                $_field_parent = field_render('parent_id', [
                    'type'   => 'select',
                    'label'  => 'Родительский тег',
                    'value'  => $entity->parent_id,
                    'values' => $_parents,
                    'class'  => 'uk-select2',
                ]);
            }
            $_form->permission = array_merge($_form->permission, $this->permissions);
            if ($entity->exists && $entity->_alias->id) {
                $_form->buttons[] = _l('', $entity->generate_url, [
                    'attributes' => [
                        'class'   => 'uk-button uk-button-success uk-margin-small-right',
                        'uk-icon' => 'icon: linkinsert_link',
                        'target'  => '_blank'
                    ]
                ]);
            }
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
                            'required'   => TRUE,
                            'attributes' => [
                                'autofocus' => TRUE,
                            ],
                        ]),
                        field_render('sub_title', [
                            'label' => 'Под заголовок',
                            'value' => $entity->getTranslation('sub_title', $this->defaultLocale)
                        ]),
                        field_render('breadcrumb_title', [
                            'label' => 'Заголовок в "Хлебных крошках"',
                            'value' => $entity->getTranslation('breadcrumb_title', $this->defaultLocale)
                        ]),
                        $_field_parent,
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
                        field_render('sort', [
                            'type'  => 'number',
                            'label' => 'Порядок сортировки',
                            'value' => $entity->exists ? $entity->sort : 0,
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->status : 1,
                            'values'   => [
                                1 => 'Опубликовано'
                            ]
                        ])
                    ],
                ],
                $this->__form_tab_display_style($entity),
                //                $this->__form_tab_media_files($entity),
                $this->__form_tab_display_rules($entity, 'pages'),
                $this->__form_tab_seo($entity),
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
                    field_render('title', [
                        'label'      => 'Заголовок',
                        'value'      => $entity->getTranslation('title', $locale),
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                        'required'   => TRUE
                    ]),
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->getTranslation('sub_title', $locale)
                    ]),
                    field_render('breadcrumb_title', [
                        'label' => 'Заголовок в "Хлебных крошках"',
                        'value' => $entity->getTranslation('breadcrumb_title', $locale)
                    ]),
                    field_render('teaser', [
                        'label'      => 'Тизер материала (краткое описание)',
                        'type'       => 'textarea',
                        'value'      => $entity->getTranslation('teaser', $locale),
                        'attributes' => [
                            'rows' => 4,
                        ]
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
            $_form->tabs[] = $this->__form_tab_seo_for_translation($entity);

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
            $_filters = [];
            $_query = Tag::from('tags as t')
                ->leftJoin('url_alias as a', 'a.model_id', '=', 't.id')
                ->when($_filter, function ($query) use ($_filter) {
                    if ($_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                    if ($_filter['parent']) $query->where('е.parent_id', $_filter['parent']);
                    if ($_filter['alias']) {
                        $query->where('a.model_type', '=', Tag::class)
                            ->where('a.alias', 'like', "%{$_filter['alias']}%");
                    }
                })
                ->where('a.model_type', Tag::class)
                ->orderByDesc('t.status')
                ->orderBy('t.title')
                ->with([
                    '_nodes',
                ])
                ->select([
                    't.*'
                ])
                ->paginate($this->entity->getPerPage(), ['t.id']);
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
                    'class' => 'uk-width-medium',
                    'data'  => 'Родительсткий тег',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: description">',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: laptop_windows">',
                ],
            ];
            if ($_user->hasPermissionTo($this->permissions['update'])) {
                $_headers[] = [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: createmode_editedit">',
                ];
            }
            $_parents = $this->entity::tree_parents();
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_item) use ($_user, $_parents) {
                    $_response = [
                        "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                        $_item->_alias->id ? _l($_item->getTranslation('title', $this->defaultLocale), $_item->_alias->alias, ['attributes' => ['target' => '_blank']]) : $_item->getTranslation('title', $this->defaultLocale),
                        ($_parents[$_item->id]['parents'] ? $_parents[$_item->id]['title_parent'] : '-//-'),
                        (string)$_item->_nodes->count(),
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_response[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->id
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-button-small uk-text-uppercase',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_response;
                });
            }
            $_filters[] = [
                'data' => field_render('title', [
                    'value'      => $_filter['title'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Заголовок'
                    ]
                ])
            ];
            if ($_parents->isNotEmpty()) {
                $_parents = $_parents->map(function ($_item) {
                    return $_item['title_option'];
                });
                if ($_parents->isNotEmpty()) $_parents->prepend('-- Выбрать --', '');
                $_filters[] = [
                    'data' => field_render('parent', [
                        'value'  => $_filter['parent'] ?? NULL,
                        'type'   => 'select',
                        'values' => $_parents,
                        'class'  => 'uk-select2',
                    ])
                ];
            }
            $_filters[] = [
                'data' => field_render('alias', [
                    'value'      => $_filter['alias'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Путь страницы'
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

            return view('backend.partials.list_items', compact('_items', '_wrap'))
                ->render();
        }

        public function store(Request $request)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if ($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if ($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->validate($request, [
                'title'   => 'required',
                'page_id' => 'sometimes|required',
            ], [], [
                'title'   => 'Заголовок ',
                'page_id' => 'Тип (Связанная страница)',
            ]);
            $_save = $request->only([
                'title',
                'sub_title',
                'breadcrumb_title',
                'body',
                'style_id',
                'style_class',
                'background_fid',
                'sort',
                'status',
                'parent_id',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item = Tag::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Tag $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if ($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if ($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->validate($request, [
                'title'   => 'required',
                'page_id' => 'sometimes|required',
            ], [], [
                'title'   => 'Заголовок ',
                'page_id' => 'Тип (Связанная страница)',
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'breadcrumb_title',
                    'teaser',
                    'body',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'breadcrumb_title',
                    'body',
                    'style_id',
                    'style_class',
                    'background_fid',
                    'sort',
                    'status',
                    'parent_id',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                    'meta_robots',
                ]);
                $_save['background_fid'] = $_background_fid['id'] ?? NULL;
                $_save['status'] = (int)($_save['status'] ?? 0);
                app()->setLocale($_locale);
                $_item->update($_save);
            }
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);

            return $this->__response_after_update($request, $_item);
        }

    }
