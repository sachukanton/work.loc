<?php

    namespace App\Http\Controllers\Dashboard\Structure;

    use App\Library\BaseController;
    use App\Models\Structure\Page;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class PageController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список страниц',
                'create'    => 'Добавить страницу',
                'edit'      => 'Редактировать страницу "<strong>:title</strong>"',
                'translate' => 'Перевод страницы на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:pages_read'
            ]);
            $this->base_route = 'pages';
            $this->permissions = [
                'read'   => 'pages_read',
                'create' => 'pages_create',
                'update' => 'pages_update',
                'delete' => 'pages_delete'
            ];
            $this->entity = new Page();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->seo = TRUE;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_field_type = NULL;
            if ($entity->exists && $entity->_alias->id) {
                $_form->buttons[] = _l('', $entity->_alias->alias, [
                    'attributes' => [
                        'class'   => 'uk-button uk-button-success uk-margin-small-right uk-text-uppercase',
                        'uk-icon' => 'icon: linkinsert_link',
                        'target'  => '_blank'
                    ]
                ]);
            }
            if (!$entity->exists) {
                $_field_type = field_render('type', [
                    'type'   => 'select',
                    'label'  => 'Тип',
                    'values' => [
                        'normal'     => 'Обычная страница',
                        'list_nodes' => 'Страница со списком материалов'
                    ],
                    'class'  => 'uk-select2'
                ]);
            }
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        field_render('locale', [
                            'type'  => 'hidden',
                            'value' => $this->defaultLocale,
                        ]),
                        field_render('title', [
                            'label'      => 'Заголовок',
                            'value'      => $entity->getTranslation('title', $this->defaultLocale),
                            'required'   => TRUE,
                            'attributes' => [
                                'autofocus' => TRUE,
                            ],
                        ]),
                        '<div class="uk-grid uk-child-width-1-2"><div>',
                        field_render('sub_title', [
                            'label' => 'Под заголовок',
                            'value' => $entity->getTranslation('sub_title', $this->defaultLocale)
                        ]),
                        '</div><div>',
                        field_render('breadcrumb_title', [
                            'label' => 'Заголовок в "Хлебных крошках"',
                            'value' => $entity->getTranslation('breadcrumb_title', $this->defaultLocale)
                        ]),
                        '</div></div>',
                        $_field_type,
//                        field_render('teaser', [
//                            'label'      => 'Тизер материала (краткое описание)',
//                            'type'       => 'textarea',
//                            'editor'     => TRUE,
//                            'value'      => $entity->getTranslation('teaser', $this->defaultLocale),
//                            'attributes' => [
//                                'rows' => 4,
//                            ]
//                        ]),
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
                            'selected' => $entity->exists ? $entity->status : 1,
                            'values'   => [
                                1 => 'Опубликовано'
                            ]
                        ])
                    ],
                ],
                $this->__form_tab_display_style($entity, 'background'),
                $this->__form_tab_media_files($entity)
            ];
            if ($entity->exists && in_array($entity->type, $entity::TYPES_USING_DEFAULT_TAGS)) {
                $_form->tabs[] = [
                    'title'   => 'Настройки SEO для материалов',
                    'content' => [
                        '<div class="uk-alert"><h4 class="uk-margin-remove-top uk-text-bold uk-text-primary">Метки для применения</h4><ul class="uk-list uk-list-small"><li><span class="uk-text-bold">[:title]</span> - название</li></ul></div>',
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Настройка страницы списка материалов</span></h3>',
                        field_render('per_page', [
                            'type'   => 'select',
                            'label'  => 'Количество выводимых элементов на страницу',
                            'value'  => $entity->per_page,
                            'class'  => 'uk-select2',
                            'values' => [
                                0  => 'Вывести все материалы',
                                2 => '2 материала на страницу',
                                8 => '8 материалов на страницу',
                                12 => '12 материалов на страницу',
                                24 => '24 материалов на страницу',
                                36 => '36 материалов на страницу',
                                48 => '48 материалов на страницу',
                            ]
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию</span></h3>',
                        field_render('tmp_meta_tags.meta_title', [
                            'label'      => 'Title',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_title', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render('tmp_meta_tags.meta_description', [
                            'type'       => 'textarea',
                            'label'      => 'Description',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_description', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render('tmp_meta_tags.meta_keywords', [
                            'type'       => 'textarea',
                            'label'      => 'Keywords',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_keywords', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ])
                    ]
                ];
            }
            $_form->tabs[] = $this->__form_tab_display_rules($entity, 'pages');
            $_form->tabs[] = $this->__form_tab_seo($entity);

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
                        'required'   => TRUE,
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->getTranslation('sub_title', $locale)
                    ]),
                    field_render('breadcrumb_title', [
                        'label' => 'Заголовок в "Хлебных крошках"',
                        'value' => $entity->getTranslation('breadcrumb_title', $locale)
                    ]),
//                    field_render('teaser', [
//                        'label'      => 'Тизер материала (краткое описание)',
//                        'type'       => 'textarea',
//                        'editor'     => TRUE,
//                        'value'      => $entity->getTranslation('teaser', $locale),
//                        'attributes' => [
//                            'rows' => 4,
//                        ]
//                    ]),
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
            if ($entity->exists && in_array($entity->type, $entity::TYPES_USING_DEFAULT_TAGS)) {
                $_form->tabs[] = [
                    'title'   => 'Настройки SEO для материалов',
                    'content' => [
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию</span></h3>',
                        field_render('tmp_meta_tags.meta_title', [
                            'label'      => 'Title',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_title', $locale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render('tmp_meta_tags.meta_description', [
                            'type'       => 'textarea',
                            'label'      => 'Description',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_description', $locale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render('tmp_meta_tags.meta_keywords', [
                            'type'       => 'textarea',
                            'label'      => 'Keywords',
                            'value'      => $entity->_tmp_meta_tags->getTranslation('meta_keywords', $locale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ])
                    ]
                ];
            }

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
            $_query = Page::from('pages as p')
                ->leftJoin('url_alias as a', 'a.model_id', '=', 'p.id')
                ->when($_filter, function ($query) use ($_filter) {
                    if ($_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                    if ($_filter['alias']) {
                        $query->where('a.model_type', '=', Page::class)
                            ->where('a.alias', 'like', "%{$_filter['alias']}%");
                    }
                })
                ->used()
                ->orderByDesc('p.status')
                ->orderBy('p.id')
                ->distinct()
                ->select([
                    'p.*'
                ])
                ->with([
                    '_alias'
                ])
                ->paginate($this->entity->getPerPage(), ['p.id']);
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
                    'data'  => 'Тип',
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
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_item) use ($_user) {
                    $_response = [
                        "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                        $_item->_alias->id ? _l($_item->getTranslation('title', $this->defaultLocale), $_item->_alias->alias, ['attributes' => ['target' => '_blank']]) : $_item->getTranslation('title', $this->defaultLocale),
                        $_item->_types($_item->type),
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
            $_filters[] = [
                'data' => field_render('title', [
                    'value'      => $_filter['title'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Заголовок'
                    ]
                ])
            ];
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

            return view('backend.partials.list_items', compact('_items', '_wrap'));
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
                'title' => 'required'
            ], [], [
                'title' => 'Заголовок'
            ]);
            $_save = $request->only([
                'title',
                'sub_title',
                'breadcrumb_title',
                'type',
//                'teaser',
                'body',
                'status',
                'style_id',
                'style_class',
                'background_fid',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item = Page::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid',
                'medias',
                'files'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Page $_item)
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
                'title' => 'required'
            ], [], [
                'title' => 'Заголовок'
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'breadcrumb_title',
//                    'teaser',
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
//                    'teaser',
                    'body',
                    'status',
                    'style_id',
                    'style_class',
                    'background_fid',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                    'meta_robots',
                    'per_page',
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

        public function destroy(Request $request, Page $_item)
        {
            if ($_item->blocked) {
                return redirect()
                    ->route("oleus.{$this->base_route}.edit", [
                        'id' => $_item->id
                    ])
                    ->with('notice', [
                        'message' => 'Элемент нельзя удалить',
                        'status'  => 'warning'
                    ]);
            }
            $_item->delete();

            return $this->__response_after_destroy($request, $_item);
        }

    }
