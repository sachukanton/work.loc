<?php

    namespace App\Http\Controllers\Dashboard\Shop;

    use App\Library\BaseController;
    use App\Models\Shop\Brand;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class BrandController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Список брендов',
                'create'    => 'Добавить бренд',
                'edit'      => 'Редактировать бренд "<strong>:title</strong>"',
                'translate' => 'Перевод бренда на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:shop_brands_read'
            ]);
            $this->base_route = 'shop_brands';
            $this->permissions = [
                'read'   => 'shop_brands_read',
                'create' => 'shop_brands_create',
                'update' => 'shop_brands_update',
                'delete' => 'shop_brands_delete'
            ];
            $this->entity = new Brand();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            if ($entity->exists && $entity->_alias->id) {
                $_form->buttons[] = _l('', $entity->_alias->alias, [
                    'attributes' => [
                        'class'   => 'uk-button uk-button-success uk-margin-small-right uk-text-uppercase',
                        'uk-icon' => 'icon: linkinsert_link',
                        'target'  => '_blank'
                    ]
                ]);
            }
            $_field_name = NULL;
            if (!$entity->exists) {
                $_field_name = @field_render('name', [
                    'label'   => 'Машинное имя бренда',
                    'help'    => 'Используется для обозначения бренда в <span class="uk-text-bold uk-text-primary">URL</span>.<br>При заполнении можно использовать символы латиского алфавита и знак подчеркивания. Если поле оставить пустым будет сгенерирован ключ из названия бренда.<br><span class="uk-text-danger">Учтите, что значение поля в дальнейшем отредактировать нельзя!</span>',
                    'form_id' => 'modal-param-item-form'
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
                        $_field_name,
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
                $this->__form_tab_display_style($entity)
            ];
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
            $_filter = $this->filter;
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_filters = [];
            $_items = collect([]);
            $_user = Auth::user();
            $_query = Brand::from('shop_brands as b')
                ->when($_filter, function ($query) use ($_filter) {
                    $query->leftJoin('url_alias as a', 'a.model_id', '=', 'b.id');
                    if ($_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                    if ($_filter['alias']) {
                        $query->where('a.model_type', '=', Brand::class)
                            ->where('a.alias', 'like', "%{$_filter['alias']}%");
                    }
                })
                ->orderByDesc('b.status')
                ->orderBy('b.id')
                ->distinct()
                ->select([
                    'b.id',
                    'b.title',
                    'b.status'
                ])
                ->with([
                    '_alias'
                ])
                ->paginate($this->entity->getPerPage(), ['b.id']);
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
            $_save = $request->only([
                'title',
                'name',
                'sub_title',
                'breadcrumb_title',
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
            if (!$request->get('name')) $request->request->remove('name');
            $this->validate($request, [
                'title' => 'required',
                'name'  => 'sometimes|required|unique:shop_brands|regex:/^[a-zA-Z0-9_-]+$/u'
            ], [], [
                'title' => 'Заголовок',
                'name'  => 'Машинное имя бренда'
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            if (is_null($_save['name'])) {
                $_save['name'] = str_slug($_save['title'], '_');
                if (Brand::where('name', $_save['name'])
                        ->count() > 0
                ) {
                    $index = 0;
                    while ($index <= 100) {
                        $_generate_name_index = "{$_save['name']}_{$index}";
                        if (Brand::where('name', $_generate_name_index)
                                ->count() == 0
                        ) {
                            $_save['name'] = $_generate_name_index;
                            break;
                        }
                        $index++;
                    }
                }
            }
            $_item = Brand::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid',
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Brand $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
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
                app()->setLocale($_locale);
                $_item->update($_save);
            }
            Session::forget([
                'background_fid',
            ]);

            return $this->__response_after_update($request, $_item);
        }

        public function destroy(Request $request, Brand $_item)
        {
            $_item->delete();

            return $this->__response_after_destroy($request, $_item);
        }

    }
