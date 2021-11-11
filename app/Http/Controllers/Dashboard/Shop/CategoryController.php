<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Models\Components\Banner;
use App\Models\Shop\AdditionalItem;
use App\Models\Shop\Category;
use App\Models\Shop\Param;
use App\Models\Shop\ParamItem;
use App\Models\ShopAdditionalItem;
use App\Models\ShopAdditionalItemPrices;
use App\Models\ShopCategory;
use App\Models\ShopParamItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Список категорий',
            'create'    => 'Добавить категорию',
            'edit'      => 'Редактировать категорию "<strong>:title</strong>"',
            'translate' => 'Перевод категории на "<strong>:locale</strong>"',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:shop_categories_read'
        ]);
        $this->base_route = 'shop_categories';
        $this->permissions = [
            'read'   => 'shop_categories_read',
            'create' => 'shop_categories_create',
            'update' => 'shop_categories_update',
            'delete' => 'shop_categories_delete'
        ];
        $this->entity = new Category();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_field_parent = NULL;
        $_field_params = NULL;
        $_parents = $this->entity::tree_parents($entity->id);
        if ($_parents->isNotEmpty()) {
            $_parents = $_parents->map(function ($_item) {
                return $_item['title_option'];
            });
            if ($_parents->isNotEmpty()) $_parents->prepend('-- Выбрать --', '');
            $_field_parent = field_render('parent_id', [
                'type'   => 'select',
                'label'  => 'Родительская категория',
                'value'  => $entity->parent_id,
                'values' => $_parents,
                'class'  => 'uk-select2',
            ]);
        }
        if ($entity->exists && $entity->_alias->id) {
            $_form->buttons[] = _l('', $entity->_alias->alias, [
                'attributes' => [
                    'class'   => 'uk-button uk-button-success uk-margin-small-right uk-text-uppercase',
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
                    '<div class="uk-grid"><div class="uk-width-1-2">',
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->getTranslation('sub_title', $this->defaultLocale)
                    ]),
                    field_render('preview_fid', [
                        'type'   => 'file',
                        'label'  => 'Изображение категории',
                        'allow'  => 'jpg|jpeg|gif|png|svg',
                        'values' => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                    ]),
                    //                    $_field_parent,
                    '</div><div class="uk-width-1-2">',
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
            ]
        ];
        if ($entity->exists) {
            $_items = $entity->_params()
                ->orderBy('sort')
                ->get();
            $_form->tabs[] = [
                'title'   => 'Параметры',
                'content' => [
                    //                    '<div class="uk-margin">',
                    //                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Использование переключение остроты</span></h3>',
                    //                    field_render('use_spicy', [
                    //                        'type'     => 'checkbox',
                    //                        'selected' => $entity->exists ? $entity->use_spicy : 0,
                    //                        'values'   => [
                    //                            1 => 'Включить'
                    //                        ]
                    //                    ]),
                    //                    '</div><hr class="uk-divider-icon">',
                    view('backend.partials.shop.category.param_items', compact('_items', 'entity'))
                        ->render()
                ]
            ];
            //            $_items = $entity->_additional_items;
            //            $_form->tabs[] = [
            //                'title'   => 'Дополнительные ингредиенты',
            //                'content' => [
            //                    view('backend.partials.shop.category.additionally_ingredients_items', compact('_items', 'entity'))
            //                        ->render()
            //                ]
            //            ];
            $_tmp_meta_tags_filter = $entity->_tmp_meta_tags()->firstOrNew([
                'type'       => 'filter',
                'model_type' => $entity->getMorphClass(),
                'model_id'   => $entity->id,
            ]);
            $_tmp_meta_tags_product = $entity->_tmp_meta_tags()->firstOrNew([
                'type'       => 'product',
                'model_type' => $entity->getMorphClass(),
                'model_id'   => $entity->id,
            ]);
            $_form->tabs[] = [
                'title'   => 'Настройки SEO для материалов',
                'content' => [
                    '<div class="uk-alert"><h4 class="uk-margin-remove-top uk-text-bold uk-text-primary">Метки для применения</h4><ul class="uk-list uk-list-small"><li><span class="uk-text-bold">[:title]</span> - название</li><li><span class="uk-text-bold">[:sku]</span> - артикул</li><li><span class="uk-text-bold">[:price]</span> - цена</li><li><span class="uk-text-bold">[:brand]</span> - производитель</li><li><span class="uk-text-bold">[:params]</span> - выбранные параметры фильтра</li></ul></div>',
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию для страниц фильтра</span></h3>',
                    field_render('tmp_meta_tags.filter.meta_title', [
                        'label'      => 'Title',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_title', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.filter.meta_description', [
                        'type'       => 'textarea',
                        'label'      => 'Description',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_description', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.filter.meta_keywords', [
                        'type'       => 'textarea',
                        'label'      => 'Keywords',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_keywords', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию для страниц товара</span></h3>',
                    field_render('tmp_meta_tags.product.meta_title', [
                        'label'      => 'Title',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_title', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.product.meta_description', [
                        'type'       => 'textarea',
                        'label'      => 'Description',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_description', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.product.meta_keywords', [
                        'type'       => 'textarea',
                        'label'      => 'Keywords',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_keywords', $this->defaultLocale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ])
                ]
            ];
            $_items = $entity->_banners()
                ->orderBy('sort')
                ->get();
            $_form->tabs[] = [
                'title'   => 'Баннер',
                'content' => [
                    view('backend.partials.shop.category.banner_items', compact('_items', 'entity'))
                        ->render()
                ]
            ];
        }
        $_form->tabs[] = $this->__form_tab_display_style($entity);
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
                field_render('menu_title', [
                    'label' => 'Название в меню',
                    'value' => $entity->getTranslation('menu_title', $locale)
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
        if ($entity->exists) {
            $_tmp_meta_tags_filter = $entity->_tmp_meta_tags()->firstOrNew([
                'type'       => 'filter',
                'model_type' => $entity->getMorphClass(),
                'model_id'   => $entity->id,
            ]);
            $_tmp_meta_tags_product = $entity->_tmp_meta_tags()->firstOrNew([
                'type'       => 'product',
                'model_type' => $entity->getMorphClass(),
                'model_id'   => $entity->id,
            ]);
            $_form->tabs[] = [
                'title'   => 'Настройки SEO для материалов',
                'content' => [
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию для страниц фильтра</span></h3>',
                    field_render('tmp_meta_tags.filter.meta_title', [
                        'label'      => 'Title',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_title', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.filter.meta_description', [
                        'type'       => 'textarea',
                        'label'      => 'Description',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_description', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.filter.meta_keywords', [
                        'type'       => 'textarea',
                        'label'      => 'Keywords',
                        'value'      => $_tmp_meta_tags_filter->getTranslation('meta_keywords', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Шаблоны SEO по умолчанию для страниц товара</span></h3>',
                    field_render('tmp_meta_tags.product.meta_title', [
                        'label'      => 'Title',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_title', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.product.meta_description', [
                        'type'       => 'textarea',
                        'label'      => 'Description',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_description', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('tmp_meta_tags.product.meta_keywords', [
                        'type'       => 'textarea',
                        'label'      => 'Keywords',
                        'value'      => $_tmp_meta_tags_product->getTranslation('meta_keywords', $locale),
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ])
                ]
            ];
        }
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
        $_query = Category::from('shop_categories as c')
            ->when($_filter, function ($query) use ($_filter) {
                $query->leftJoin('url_alias as a', 'a.model_id', '=', 'c.id')
                    ->where('a.model_type', '=', Category::class);
                if ($_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                if ($_filter['parent']) $query->where('c.parent_id', $_filter['parent']);
                if ($_filter['alias']) {
                    $query->where('a.alias', 'like', "%{$_filter['alias']}%");
                }
            })
            ->orderByDesc('c.status')
            ->orderBy('c.id')
            ->distinct()
            ->select([
                'c.*'
            ])
            ->with([
                '_alias'
            ])
            ->paginate($this->entity->getPerPage(), ['c.id']);
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
            //            [
            //                'class' => 'uk-width-medium',
            //                'data'  => 'Родительсткая категория',
            //            ],
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
                    //                    ($_parents[$_item->id]['parents'] ? $_parents[$_item->id]['title_parent'] : '-//-'),
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

        return view('backend.partials.list_items', compact('_items', '_wrap'));
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
        $this->validate($request, [
            'title' => 'required'
        ], [], [
            'title' => 'Заголовок'
        ]);
        $_save = $request->only([
            'title',
            'sub_title',
            'parent_id',
            'preview_fid',
            'breadcrumb_title',
            'menu_title',
            'body',
            'status',
            'style_id',
            'style_class',
            'code_1c',
            'sort',
            'background_fid',
            'modify_param',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'meta_robots',
            'use_spicy',
        ]);
        $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
        $_save['background_fid'] = $_background_fid['id'] ?? NULL;
        $_save['status'] = (int)($_save['status'] ?? 0);
        $_save['use_spicy'] = (int)($_save['use_spicy'] ?? 0);
        $_item = Category::updateOrCreate([
            'id' => NULL
        ], $_save);
        Session::forget([
            'background_fid',
            'preview_fid',
        ]);

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, Category $_item)
    {
        if ($background_fid = $request->input('background_fid')) {
            $_background_fid = array_shift($background_fid);
            Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
        }
        if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
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
                'menu_title',
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
                'menu_title',
                'parent_id',
                'body',
                'status',
                'sort',
                'code_1c',
                'style_id',
                'modify_param',
                'style_class',
                'background_fid',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'use_spicy',
            ]);
            $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['use_spicy'] = (int)($_save['use_spicy'] ?? 0);
            app()->setLocale($_locale);
            $_item->update($_save);
            if ($_params = $request->get('params')) {
                foreach ($_params as $_param_id => &$_param_data) {
                    $_param_data['visible_in_filter'] = isset($_param_data['visible_in_filter']) ? (int)($_param_data['visible_in_filter'] ?? 0) : 0;
                    $_param_data['collapse'] = isset($_param_data['collapse']) ? (int)($_param_data['collapse'] ?? 0) : 0;
                }
                $_item->_params()->sync($_params);
            }
            if ($_banners = $request->get('banners')) {
                $_item->_banners()->sync($_banners);
            }
        }
        Session::forget([
            'background_fid',
            'preview_fid',
        ]);

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Category $_item)
    {
        $_item->_params()->detach();
        $_item->_files_related()->detach();
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function param(Request $request, Category $entity, $action, Param $item)
    {
        $commands = [];
        switch ($action) {
            case 'add':
                $_items = Param::all();
                $_category_param = $entity->_params->keyBy('id');
                $_items = $_items->filter(function ($_param) use ($_category_param) {
                    return !$_category_param->has($_param->id);
                })->keyBy('id')
                    ->map(function ($_param) {
                        return $_param->getTranslation('title', $this->defaultLocale);
                    });
                if ($_items->isNotEmpty()) {
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.shop.category.param_item_modal', compact('_items', 'entity'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                } else {
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => 'Список параметров для добавления пуст.',
                            'status' => 'warning',
                        ]
                    ];
                }
                break;
            case 'save':
                $_save = $request->only([
                    'params',
                ]);
                $validate_rules = [
                    'params' => 'required'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    'params' => 'Параметры'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-category-param-item-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-category-param-item-form'),
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
                    $_attach = NULL;
                    foreach ($_save['params'] as $_param) $_attach[$_param] = [];
                    if ($_attach) $entity->_params()->attach($_attach);
                    $_items = $entity->_params()
                        ->orderBy('sort')
                        ->get();
                    $_items_output = view('backend.partials.shop.category.param_item', compact('_items', 'entity'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-category-params-items',
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
                $entity->_params()
                    ->detach($item->id);
                $_items = $entity->_params()
                    ->orderBy('sort')
                    ->get();
                if ($_items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.category.param_item', compact('_items', 'entity'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-category-params-items',
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

    public function banner(Request $request, Category $entity, $action, Banner $item)
    {
        $commands = [];
        switch ($action) {
            case 'add':
                $_items = Banner::all();
                $_category_banner = $entity->_banners->keyBy('id');
                $_items = $_items->filter(function ($_banner) use ($_category_banner) {
                    return !$_category_banner->has($_banner->id);
                })->keyBy('id')
                    ->map(function ($_banner) {
                        return $_banner->title;
                    });
                if ($_items->isNotEmpty()) {
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.shop.category.banner_item_modal', compact('_items', 'entity'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                } else {
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => 'Список баннер для добавления пуст.',
                            'status' => 'warning',
                        ]
                    ];
                }
                break;
            case 'save':
                $_save = $request->only([
                    'banners',
                ]);
                $validate_rules = [
                    'banners' => 'required'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    'banners' => 'Баннеры'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-category-param-banner-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-category-banner-item-form'),
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
                    $_attach = NULL;
                    foreach ($_save['banners'] as $_banner) $_attach[$_banner] = [];
                    if ($_attach) $entity->_banners()->attach($_attach);
                    $_items = $entity->_banners()
                        ->orderBy('sort')
                        ->get();
                    $_items_output = view('backend.partials.shop.category.banner_item', compact('_items', 'entity'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-category-banners-items',
                            'data'   => $_items_output
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'addClass',
                        'options' => [
                            'target' => '#list-category-banners-items + div > a',
                            'data'   => 'uk-hidden'
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
                $entity->_banners()
                    ->detach($item->id);
                $_items = $entity->_banners()
                    ->orderBy('sort')
                    ->get();
                if ($_items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.category.banner_item', compact('_items', 'entity'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#list-category-banners-items + div > a',
                            'data'   => 'uk-hidden'
                        ]
                    ];
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-category-banners-items',
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

    public function additional_item(Request $request, Category $category, $action, AdditionalItem $item)
    {
        $commands = [];
        switch ($action) {
            case 'add':
            case 'edit':
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content' => view('backend.partials.shop.category.additionally_ingredients_item_modal', compact('item', 'category'))
                            ->render()
                    ]
                ];
                $commands['commands'][] = [
                    'command' => 'easyAutocomplete'
                ];
                break;
            case 'save':
            case 'update':
                $_save = $request->input('item');
                $validate_rules = [
                    'item.item_id' => 'required',
                    'item.sku'     => 'required',
                    'item.price'   => 'required',
                    'item.value'   => 'required',
                ];
                $validator = Validator::make($request->all(), $validate_rules);
                foreach ($validate_rules as $field => $rule) {
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'   => 'uk-form-danger'
                        ]
                    ];
                }
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                                'data'   => 'uk-form-danger'
                            ]
                        ];
                    }
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => trans('notice.errors')
                        ]
                    ];
                } else {
                    $_id_item = $_save['id'];
                    $_save['default'] = (int)($_save['default'] ?? 0);
                    AdditionalItem::updateOrCreate([
                        'id' => $_id_item
                    ], $_save);
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-additionally-ingredients-select-items',
                            'data'   => view('backend.partials.shop.category.additionally_ingredients_item', ['_items' => $category->_additional_items])
                                ->render()
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'success',
                            'text'   => 'Дополнительный ингредиент добавлен'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_modalClose'
                    ];
                }
                break;
            case 'destroy':
                $item->delete();
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-additionally-ingredients-select-items',
                        'data'   => view('backend.partials.shop.category.additionally_ingredients_item', ['_items' => $category->_additional_items])
                            ->render()
                    ]
                ];
                $commands['commands'][] = [
                    'command' => 'UK_modalClose'
                ];
                break;
        }

        return response($commands, 200);
    }
}
