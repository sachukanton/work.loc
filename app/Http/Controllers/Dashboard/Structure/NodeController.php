<?php

    namespace App\Http\Controllers\Dashboard\Structure;

    use App\Library\BaseController;
    use App\Library\Dashboard;
    use App\Models\Structure\Node;
    use App\Models\User\User;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class NodeController extends BaseController
    {

        protected $types;
        protected $authors;

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список материалов',
                'create'    => 'Добавить материал',
                'edit'      => 'Редактировать материал "<strong>:title</strong>"',
                'translate' => 'Перевод материала на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:nodes_read'
            ]);
            $this->types = Node::nodeTypes();
            $this->tags = Node::Tags();
            $this->authors = User::_authors();
            $this->base_route = 'nodes';
            $this->permissions = [
                'read'   => 'nodes_read',
                'create' => 'nodes_create',
                'update' => 'nodes_update',
                'delete' => 'nodes_delete',
            ];
            $this->entity = new Node();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->seo = TRUE;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_tags = [];
            $_field_type = NULL;
            $_field_tags = NULL;
            if ($entity->exists && $entity->_alias->id) {
                $_form->buttons[] = _l('', $entity->generate_url, [
                    'attributes' => [
                        'class'   => 'uk-button uk-button-success uk-margin-small-right',
                        'uk-icon' => 'icon: linkinsert_link',
                        'target'  => '_blank'
                    ]
                ]);
            }
            if ($this->tags) {
                $_tags = $this->tags->keyBy('id')->map(function ($_type) {
                    return $_type->getTranslation('title', $this->defaultLocale);
                });
            }
            $_field_tags = field_render('tags', [
                'type'     => 'select',
                'label'    => 'Теги',
                'selected' => $entity->_tags->isNotEmpty() ? $entity->_tags->pluck('id') : [],
                'values'   => $_tags,
                'multiple' => TRUE,
                'class'    => 'uk-select2',
                'options'  => 'data-minimum-results-for-search="5" data-tags="true"'
            ]);
            if ($this->types) {
                $_types = $this->types->keyBy('id')->map(function ($_type) {
                    return $_type->getTranslation('title', $this->defaultLocale);
                });
                $_field_type = field_render('page_id', [
                    'type'     => 'select',
                    'label'    => 'Тип (Связанная страница)',
                    'value'    => $entity->page_id,
                    'values'   => $_types,
                    'class'    => 'uk-select2',
                    'required' => TRUE,
                    'help'     => 'Определяет к какому типу будет относится материал'
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
                        '<div class="uk-grid"><div class="uk-width-1-3">',
                        field_render('preview_fid', [
                            'type'   => 'file',
                            'label'  => 'Изображение в списке',
                            'allow'  => 'jpg|jpeg|gif|png',
                            'values' => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                        ]),
                        '</div><div class="uk-width-2-3">',
                        field_render('sub_title', [
                            'label' => 'Под заголовок',
                            'value' => $entity->getTranslation('sub_title', $this->defaultLocale)
                        ]),
                        field_render('breadcrumb_title', [
                            'label' => 'Заголовок в "Хлебных крошках"',
                            'value' => $entity->getTranslation('breadcrumb_title', $this->defaultLocale)
                        ]),
                        $_field_type,
                        $_field_tags,
                        '</div></div>',
//                        field_render('teaser', [
//                            'label'      => 'Тизер материала (краткое описание)',
//                            'type'       => 'textarea',
//                            'editor'     => TRUE,
//                            'class'      => 'editor-short',
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
                            ],
//                            'required'   => TRUE
                        ]),
                        '<hr class="uk-divider-icon">',
                        '<div class="uk-grid uk-child-width-1-3"><div>',
                        field_render('published_at', [
                            'label'      => 'Дата публикации',
                            'value'      => $entity->exists && $entity->published_at ? $entity->published_at->format('d.m.Y') : Carbon::now()->format('d.m.Y'),
                            'class'      => 'uk-datepicker',
                            'attributes' => [
                                'data-position' => 'top left'
                            ]
                        ]),
                        '</div><div>',
                        field_render('user_id', [
                            'type'   => 'select',
                            'label'  => 'Автор',
                            'value'  => $entity->user_id,
                            'values' => $this->authors->pluck('full_name', 'id'),
                            'class'  => 'uk-select2',
                        ]),
                        '</div><div>',
                        field_render('sort', [
                            'type'  => 'number',
                            'label' => 'Порядок сортировки',
                            'value' => $entity->exists ? $entity->sort : 0,
                        ]),
                        '</div></div>',
                        field_render('visible_on_list', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->visible_on_list : 1,
                            'values'   => [
                                1 => 'Выводить в списке материалов'
                            ]
                        ]),
                        field_render('visible_on_block', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->visible_on_block : 1,
                            'values'   => [
                                1 => 'Выводить в блок последних материалов'
                            ]
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
                $this->__form_tab_media_files($entity),
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
//                    field_render('teaser', [
//                        'label'      => 'Тизер материала (краткое описание)',
//                        'type'       => 'textarea',
//                        'editor'     => TRUE,
//                        'class'      => 'editor-short',
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
            $_query = Node::from('nodes as n')
                ->leftJoin('url_alias as a', 'a.model_id', '=', 'n.id')
                ->when($_filter, function ($query) use ($_filter) {
                    if (isset($_filter['page_id']) && $_filter['page_id']) $query->where('n.page_id', $_filter['page_id']);
                    if (isset($_filter['title']) && $_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                })
                ->where('a.model_type', Node::class)
                ->orderByDesc('n.status')
                ->orderByDesc('n.published_at')
                ->orderByDesc('n.updated_at')
                ->orderBy('n.title')
                ->with([
                    '_page',
                    '_tags'
                ])
                ->select([
                    'n.*'
                ])
                ->paginate($this->entity->getPerPage(), ['n.id']);
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
                    'class' => 'uk-text-center',
                    'style' => 'width: 120px',
                    'data'  => '<span uk-icon="icon: date_range">',
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
                        $_item->_page ? _l($_item->_page->getTranslation('title', $this->defaultLocale), 'oleus.nodes', [
                            'p' => ['page_id' => $_item->_page->id]
                        ]) : ' - ',
                        $_item->published_at ? $_item->published_at->format('d.m.Y') : $_item->updated_at->format('d.m.Y'),
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
            if ($this->types->isNotEmpty()) {
                $_filters[] = [
                    'data' => field_render('title', [
                        'value'      => $_filter['title'] ?? NULL,
                        'attributes' => [
                            'placeholder' => 'Заголовок'
                        ]
                    ])
                ];
                $_filters[] = [
                    'data' => field_render('page_id', [
                        'type'   => 'select',
                        'value'  => $_filter['page_id'] ?? 0,
                        'values' => $this->types->pluck('title', 'id')->prepend('- выбрать -', 0),
                        'class'  => 'uk-select2',
                    ])
                ];
            }
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

        public function create(Node $_item)
        {
            if ($this->types->isEmpty()) {
                return redirect()
                    ->route('oleus.nodes')
                    ->with('notice', [
                        'message' => 'Для начала добавьте страницу вывода материаллов в разделе \"Страниц\"',
                        'status'  => 'warning'
                    ]);
            }
            $_wrap = $this->render([
                'seo.title' => $this->titles['create']
            ]);
            $_form = $this->_form($_item);

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }

        public function store(Request $request)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if ($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
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
//                'body'    => 'required',
                'page_id' => 'sometimes|required',
            ], [], [
                'title'   => 'Заголовок ',
//                'body'    => 'Содержимое',
                'page_id' => 'Тип (Связанная страница)',
            ]);
            $_save = $request->only([
                'title',
                'sub_title',
                'breadcrumb_title',
                'page_id',
                'user_id',
//                'teaser',
                'body',
                'style_id',
                'style_class',
                'preview_fid',
                'background_fid',
                'published_at',
                'sort',
                'status',
                'visible_on_list',
                'visible_on_block',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
            ]);
            $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['published_at'] = $_save['published_at'] ? Carbon::parse($_save['published_at']) : Carbon::now();
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['visible_on_block'] = (int)($_save['visible_on_block'] ?? 0);
            $_save['visible_on_list'] = (int)($_save['visible_on_list'] ?? 0);
            $_item = Node::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Node $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if ($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
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
//                'body'    => 'required',
                'page_id' => 'sometimes|required',
            ], [], [
                'title'   => 'Заголовок ',
//                'body'    => 'Содержимое',
                'page_id' => 'Тип (Связанная страница)',
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
                    'page_id',
                    'user_id',
//                    'teaser',
                    'body',
                    'style_id',
                    'style_class',
                    'preview_fid',
                    'background_fid',
                    'published_at',
                    'sort',
                    'status',
                    'visible_on_list',
                    'visible_on_block',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                    'meta_robots',
                ]);
                $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
                $_save['background_fid'] = $_background_fid['id'] ?? NULL;
                $_save['published_at'] = $_save['published_at'] ? Carbon::parse($_save['published_at']) : Carbon::now();
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_save['visible_on_block'] = (int)($_save['visible_on_block'] ?? 0);
                $_save['visible_on_list'] = (int)($_save['visible_on_list'] ?? 0);
                app()->setLocale($_locale);
                $_item->update($_save);
            }
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);

            return $this->__response_after_update($request, $_item);
        }

    }
