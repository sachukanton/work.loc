<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Models\Shop\Category;
use App\Models\Shop\FilterPage;
use App\Models\Shop\Param;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FilterPageController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Список страниц фильтра',
            'create'    => 'Сохранить страницу',
            'edit'      => 'Редактировать страницу фильтра "<strong>:title</strong>"',
            'translate' => 'Перевод страницы на "<strong>:locale</strong>"',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:shop_categories_read'
        ]);
        $this->base_route = 'shop_filter_pages';
        $this->permissions = [
            'read'   => 'shop_categories_read',
            'create' => 'shop_categories_create',
            'update' => 'shop_categories_update',
            'delete' => 'shop_categories_delete'
        ];
        $this->entity = new FilterPage();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        if ($entity->exists) {
            $_form->buttons[] = _l('', _u($entity->alias), [
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
                    field_render('base_path', [
                        'type'  => 'hidden',
                        'value' => $entity->exists ? $entity->base_path : request()->get('alias'),
                    ]),
                    field_render('category_id', [
                        'type'  => 'hidden',
                        'value' => $entity->exists ? $entity->category_id : request()->get('category'),
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
                    '<div uk-grid class="uk-child-width-1-2"><div>',
                    field_render('breadcrumb_title', [
                        'label' => 'Заголовок в "Хлебных крошках"',
                        'value' => $entity->getTranslation('breadcrumb_title', $this->defaultLocale)
                    ]),
                    '</div><div>',
                    field_render('menu_title', [
                        'label' => 'Название пункта меню',
                        'value' => $entity->getTranslation('menu_title', $this->defaultLocale)
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
                    ])
                ],
            ]
        ];
        if ($entity->exists) {
            $_items = $entity->_pages()
                ->orderBy('sort')
                ->get();
            $_form->tabs[] = [
                'title'   => 'Связанные страницы',
                'content' => [
                    view('backend.partials.shop.filter_page.page_items', compact('_items', 'entity'))
                        ->render()
                ],
            ];
        }
        $_form->tabs[] = $this->__form_tab_display_style($entity);
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
                    'label' => 'Название пункта меню',
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
        $_query = FilterPage::from('shop_filter_pages as fp')
            ->when($_filter, function ($query) use ($_filter) {
                if ($_filter['category']) $query->where('fp.category_id', $_filter['category']);
                if ($_filter['alias']) $query->where('a.alias', 'like', "%{$_filter['alias']}%");
            })
            ->orderBy('fp.id')
            ->select([
                'fp.*'
            ])
            ->paginate($this->entity->getPerPage(), ['fp.id']);
        $_buttons = [];
        $_headers = [
            [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => 'ID',
            ],
            [
                'data' => 'Заголовок',
            ],
            //                [
            //                    'class' => 'uk-width-medium',
            //                    'data'  => 'Категория',
            //                ],
        ];
        if ($_user->hasPermissionTo($this->permissions['update'])) {
            $_headers[] = [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: createmode_editedit">',
            ];
        }
        $_parents = Category::tree_parents();
        if ($_query->isNotEmpty()) {
            $_items = $_query->map(function ($_item) use ($_user, $_parents) {
                $_response = [
                    "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                    $_item->getTranslation('title', $this->defaultLocale),
                    //                        ($_parents[$_item->category_id] ? $_parents[$_item->category_id]['title_option'] : '-//-'),
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
        if ($_parents->isNotEmpty()) {
            $_parents = $_parents->map(function ($_item) {
                return $_item['title_option'];
            });
            if ($_parents->isNotEmpty()) $_parents->prepend('-- Выбрать --', '');
            $_filters[] = [
                'data' => field_render('category', [
                    'value'  => $_filter['category'] ?? NULL,
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
        $this->validate($request, [
            'title' => 'required'
        ], [], [
            'title' => 'Заголовок'
        ]);
        $_save = $request->only([
            'title',
            'sub_title',
            'category_id',
            'breadcrumb_title',
            'menu_title',
            'body',
            'style_id',
            'style_class',
            'base_path',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'meta_robots',
        ]);
        $_item = FilterPage::updateOrCreate([
            'id' => NULL
        ], $_save);

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, FilterPage $_item)
    {
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
                //                    'base_path',
                'body',
                'style_id',
                'style_class',
                'menu_title',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
            ]);
            app()->setLocale($_locale);
            $_item->update($_save);
            if ($_pages = $request->get('page')) {
                $_item->_pages()->sync($_pages);
            }
        }

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, FilterPage $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function page(Request $request, FilterPage $entity, $action, FilterPage $item = NULL)
    {
        $commands = [];
        switch ($action) {
            case 'add':
                $_items = FilterPage::where('id', '<>', $entity->id)
                    ->get([
                        'id',
                        'title',
                    ]);
                $_category_param = $entity->_pages->keyBy('id');
                $_items = $_items->filter(function ($_page) use ($_category_param) {
                    return !$_category_param->has($_page->id);
                })->keyBy('id')
                    ->map(function ($_page) {
                        return $_page->getTranslation('title', $this->defaultLocale);
                    });
                if ($_items->isNotEmpty()) {
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.shop.filter_page.page_item_modal', compact('_items', 'entity'))
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
                    'pages',
                ]);
                $validate_rules = [
                    'pages' => 'required'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    'pages' => 'Связанные страницы'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-filter-page-item-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-filter-page-item-form'),
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
                    foreach ($_save['pages'] as $_param) $_attach[$_param] = [];
                    if ($_attach) $entity->_pages()->attach($_attach);
                    $_items = $entity->_pages()
                        ->orderBy('sort')
                        ->get();
                    $_items_output = view('backend.partials.shop.filter_page.page_item', compact('_items', 'entity'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-filter-pages-items',
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
                $entity->_pages()
                    ->detach($item->id);
                $_items = $entity->_pages()
                    ->orderBy('sort')
                    ->get();
                if ($_items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.filter_page.page_item', compact('_items', 'entity'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-filter-pages-items',
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

}
