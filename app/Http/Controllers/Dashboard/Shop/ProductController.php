<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Models\Seo\UrlAlias;
use App\Models\Shop\AdditionalItem;
use App\Models\Shop\Brand;
use App\Models\Shop\Category;
use App\Models\Shop\Param;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Список товаров',
            'create'    => 'Добавить товар',
            'edit'      => 'Редактировать товар "<strong>:title</strong>"',
            'translate' => 'Перевод товара на "<strong>:locale</strong>"',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:shop_products_read'
        ]);
        $this->base_route = 'shop_products';
        $this->permissions = [
            'read'   => 'shop_products_read',
            'create' => 'shop_products_create',
            'update' => 'shop_products_update',
            'delete' => 'shop_products_delete'
        ];
        $this->entity = new Product();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_field_params = NULL;
        $_field_brands = NULL;
        $_field_prices = NULL;
        $_brands = Brand::get([
            'id',
            'title'
        ])->keyBy('id');
        if ($_brands->isNotEmpty()) {
            $_brands = $_brands->map(function ($_item) {
                return $_item->getTranslation('title', $this->defaultLocale);
            });
            if ($_brands->isNotEmpty()) $_brands->prepend('-- Выбрать --', '');
            $_field_brands = field_render('brand_id', [
                'type'   => 'select',
                'label'  => 'Брэнд',
                'value'  => $entity->brand_id,
                'values' => $_brands,
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
        $_categories = Category::tree_parents();
        if ($_categories->isNotEmpty()) {
            $_categories = $_categories->map(function ($_item) {
                return $_item['title_option'];
            });
        }
        if ($entity->exists) {
            $_i = 0;
            $_field_prices = [];
            foreach ($entity->_prices as $_price) {
                $_field_prices = array_merge([], [
                    '<h3 class="uk-heading-line uk-text-uppercase uk-margin-remove-top"><span>Цены</span></h3>',
                    field_render("prices.{$entity->_price->id}.id", [
                        'type'  => 'hidden',
                        'value' => $entity->_price->id,
                    ]),
                    field_render("prices.{$entity->_price->id}.location", [
                        'type'  => 'hidden',
                        'value' => $entity->_price->location,
                    ]),
                    '<div uk-grid class="uk-child-width-1-3"><div>',
                    field_render("prices.{$entity->_price->id}.price", [
                        'type'       => 'number',
                        'label'      => 'Актуальная цена',
                        'value'      => $entity->_price->price,
                        'attributes' => [
                            'min'  => 0,
                            'step' => 0.01
                        ],
                    ]),
                    '</div><div>',
                    field_render("prices.{$entity->_price->id}.old_price", [
                        'type'       => 'number',
                        'label'      => 'Старая цена',
                        'value'      => $entity->_price->old_price,
                        'attributes' => [
                            'min'  => 0,
                            'step' => 0.01
                        ]
                    ]),
                    '</div><div>',
                    field_render("prices.{$entity->_price->id}.discount_price", [
                        'type'       => 'number',
                        'label'      => 'Акционная цена',
                        'value'      => $entity->_price->discount_price,
                        'attributes' => [
                            'min'  => 0,
                            'step' => 0.01
                        ]
                    ]),
                    '</div></div>',
                    '<h3 class="uk-heading-line uk-text-uppercase"><span>Наличие</span></h3>',
                    field_render("prices.{$entity->_price->id}.status", [
                        'type'   => 'radio',
                        'label'  => 'Состояние',
                        'value'  => $entity->_price->status,
                        'values' => [
                            //                            'in_stock'      => 'Есть в наличии',
                            'not_limited'   => 'В наличии без ограничения',
                            //                            'under_order'   => 'Под заказ',
                            'not_available' => 'Нет в наличии',
                        ]
                    ]),
                    //                    field_render("prices.{$entity->_price->id}.quantity", [
                    //                        'type'       => 'number',
                    //                        'label'      => 'Количество в наличи',
                    //                        'value'      => $entity->_price->_quantity->quantity > 0 ? $entity->_price->_quantity->quantity : NULL,
                    //                        'attributes' => [
                    //                            'min'  => 0,
                    //                            'step' => 1
                    //                        ],
                    //                        'help'       => 'Используется, если указано <span class="uk-text-bold">"Состояние"</span> как <span class="uk-text-bold">"Есть в наличии"</span>'
                    //                    ]),
                ]);
                $_i++;
            }
        } else {
            $_field_prices = [
                '<h3 class="uk-heading-line uk-text-uppercase uk-margin-remove-top"><span>Цены</span></h3>',
                field_render("prices.0.location", [
                    'type'  => 'hidden',
                    'value' => NULL,
                ]),
                field_render("prices.0.id", [
                    'type'  => 'hidden',
                    'value' => NULL,
                ]),
                '<div uk-grid class="uk-child-width-1-3"><div>',
                field_render("prices.0.price", [
                    'type'       => 'number',
                    'label'      => 'Актуальная цена',
                    'value'      => NULL,
                    'attributes' => [
                        'min'  => 0,
                        'step' => 0.01
                    ],
                ]),
                '</div><div>',
                field_render("prices.0.old_price", [
                    'type'       => 'number',
                    'label'      => 'Старая цена',
                    'value'      => NULL,
                    'attributes' => [
                        'min'  => 0,
                        'step' => 0.01
                    ]
                ]),
                '</div><div>',
                field_render("prices.0.discount_price", [
                    'type'       => 'number',
                    'label'      => 'Акционная цена',
                    'value'      => NULL,
                    'attributes' => [
                        'min'  => 0,
                        'step' => 0.01
                    ]
                ]),
                '</div></div>',
                '<h3 class="uk-heading-line uk-text-uppercase"><span>Наличие</span></h3>',
                field_render("prices.0.status", [
                    'type'   => 'radio',
                    'label'  => 'Состояние',
                    'value'  => 'not_available',
                    'values' => [
                        //                        'in_stock'      => 'Есть в наличии',
                        'not_limited'   => 'В наличии без ограничения',
                        'not_available' => 'Нет в наличии',
                        //                        'under_order'   => 'Под заказ',
                    ]
                ]),
                //                field_render("prices.0.quantity", [
                //                    'type'       => 'number',
                //                    'label'      => 'Количество в наличи',
                //                    'value'      => 0,
                //                    'attributes' => [
                //                        'min'  => 0,
                //                        'step' => 1
                //                    ],
                //                    'help'       => 'Используется, если указано <span class="uk-text-bold">"Состояние"</span> как <span class="uk-text-bold">"Есть в наличии"</span>'
                //                ]),
            ];
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

                    '</div><div class="uk-width-1-2">',

                    '</div></div>',
                    '<div class="uk-grid"><div class="uk-width-1-3">',
                    field_render('preview_fid', [
                        'type'   => 'file',
                        'label'  => 'Изображение товара',
                        'allow'  => 'jpg|jpeg|gif|png',
                        'values' => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                        'help'   => 'Рекомендуемый размер изображения 600px/400px'
                    ]),
                    field_render('full_fid', [
                        'type'   => 'file',
                        'label'  => 'Изображение товара (крупное фото)',
                        'allow'  => 'jpg|jpeg|gif|png',
                        'values' => $entity->exists && $entity->_preview_full ? [$entity->_preview_full] : NULL,
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
                    '</div>',
                    '</div>',
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
            [
                'title'   => 'Товар',
                'content' => [
                    '<div class="uk-grid uk-child-width-1-2"><div>',
                    '<div class="uk-grid uk-child-width-1-2"><div>',
                    field_render('sku', [
                        'label' => 'Артикул',
                        'value' => $entity->sku,
                    ]),
                    '</div><div>',
                    field_render('iiko_id', [
                        'label' => 'Код товара в IIKO',
                        'value' => $entity->iiko_id
                    ]),
                    '</div></div>',
                    '</div><div>',
                    //                    $_field_brands,
                    '</div></div>',
                    '<div class="uk-margin">',
                    '<h3 class="uk-heading-line uk-text-uppercase uk-text-color-green"><span><span uk-icon="icon:check;"></span> Хиты</span></h3>',
                    field_render('mark_new', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->mark_new : 0,
                        'values'   => [
                            1 => 'Новый товар'
                        ]
                    ]),
                    //                        field_render('mark_hit', [
                    //                            'type'     => 'checkbox',
                    //                            'selected' => $entity->exists ? $entity->mark_hit : 0,
                    //                            'values'   => [
                    //                                1 => 'Популярный товар'
                    //                            ]
                    //                        ]),
                    field_render('mark_recommended_front', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->mark_recommended_front : 0,
                        'values'   => [
                            1 => 'Популярное (главная страница)'
                        ]
                    ]),
                    field_render('mark_recommended_checkout', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->mark_recommended_checkout : 0,
                        'values'   => [
                            1 => 'Рекомендованный товар (оформление заказа)'
                        ]
                    ]),
                    '</div>',
                    //                    '<div class="uk-margin"><h3 class="uk-heading-line uk-text-uppercase"><span>Размер карточки</span></h3>',
                    //                    field_render('double_card', [
                    //                        'type'     => 'checkbox',
                    //                        'selected' => $entity->exists ? $entity->double_card : 0,
                    //                        'values'   => [
                    //                            1 => 'Двойная карточка'
                    //                        ]
                    //                    ]),
                    //                    '</div>',
                    //                    '<div class="uk-margin"><h3 class="uk-heading-line uk-text-uppercase"><span>Переключение остроты</span></h3>',
                    //                    field_render('use_spicy', [
                    //                        'type'     => 'checkbox',
                    //                        'selected' => $entity->exists ? $entity->use_spicy : 0,
                    //                        'values'   => [
                    //                            1 => 'Включить возможность выбирать'
                    //                        ]
                    //                    ]),
                    //                    '</div>',
                    //                    '<div class="uk-margin"><h3 class="uk-heading-line uk-text-uppercase"><span>Список ингредиентов количество которых можно менять</span></h3>',
                    //                    field_render('variable_ingredients', [
                    //                        'type'     => 'select',
                    //                        'selected' => $entity->exists ? $entity->variable_ingredients : [],
                    //                        'values'   => $entity::getIngredientsList(),
                    //                        'class'    => 'uk-select2',
                    //                        'multiple' => TRUE,
                    //                        'options'  => 'data-minimum-results-for-search="5"'
                    //                    ]),
                    //                    '</div>',
                    view('backend.partials.shop.product.categories', compact('_categories', 'entity'))
                        ->render()
                ]
            ],
            [
                'title'   => 'Цены',
                'content' => $_field_prices
            ],
            //            $entity->exists ? [
            //                'title'   => 'Модификации товара',
            //                'content' => [
            //                    view('backend.partials.shop.product.modifications', compact('entity'))
            //                        ->render()
            //                ]
            //            ] : NULL,
            //            [
            //                'title'   => 'Продукты/Начинки',
            //                'content' => [
            //                    field_render('specifications', [
            //                        'type'    => 'table',
            //                        'value'   => $entity->getTranslation('specifications', $this->defaultLocale),
            //                        'options' => [
            //                            'thead' => [
            //                                'Название параметра',
            //                                'Значение параметра'
            //                            ]
            //                        ],
            //                        'help'    => 'При заполнении только крайней левой ячейки происходит объединение ячеек строки.'
            //                    ]),
            //                ]
            //            ],
        ];
        if ($entity->exists) {
            $type = 'related';
            $_items = $entity->_product_related('related', TRUE);
            $_form->tabs[] = [
                'title'   => 'Сопотствующие товары',
                'content' => [
                    view('backend.partials.shop.product.related', compact('_items', 'entity', 'type'))
                        ->render()
                ]
            ];
        }
        if ($entity->exists) {
            $type = 'consist';
            $_items = $entity->_product_consist('consist', TRUE);
            $_form->tabs[] = [
                'title'   => 'В состав входят',
                'content' => [
                    view('backend.partials.shop.product.consists', compact('_items', 'entity', 'type'))
                        ->render()
                ]
            ];
        }
        $_form->tabs[] = $this->__form_tab_media_files($entity);
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
        $_form->tabs = [
            [
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
                ],
            ],
            //            [
            //                'title'   => 'Спецификация',
            //                'content' => [
            //                    field_render('specifications', [
            //                        'type'    => 'table',
            //                        'value'   => $entity->getTranslation('specifications', $locale),
            //                        'options' => [
            //                            'thead' => [
            //                                'Название параметра',
            //                                'Значение параметра'
            //                            ]
            //                        ],
            //                        'help'    => 'При заполнении только крайней левой ячейки происходит объединение ячеек строки.'
            //                    ]),
            //                ]
            //            ],
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
        $_query = Product::from('shop_products as p')
            ->leftJoin('url_alias as a', 'a.model_id', '=', 'p.id')
            ->when($_filter, function ($query) use ($_filter) {
                $query->leftJoin('shop_product_category as c', 'c.model_id', '=', 'p.id');
                if (isset($_filter['category']) && $_filter['category']) {
                    if ($_filter['category'] == -1) {
                        $query->whereNull('c.category_id');
                    } else {
                        $_query_categories = Category::find($_filter['category']);
                        $_query_categories_children = $_query_categories->all_children;
                        $_query_categories_children->put($_query_categories->id, $_query_categories);
                        $query->whereIn('c.category_id', $_query_categories_children->pluck('id'));
                    }
                }
                if (isset($_filter['title']) && $_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                if (isset($_filter['alias']) && $_filter['alias']) $query->where('a.alias', 'like', "%{$_filter['alias']}%");
            })
            ->where('a.model_type', '=', Product::class)
            ->whereRaw(DB::raw('p.modify = p.id'))
            ->distinct()
            ->select([
                'p.id',
                'p.modify',
                'p.title',
                'p.status',
                'p.double_card',
                'p.sort',
            ])
            ->with([
                '_alias',
                '_category',
                '_prices',
            ])
            ->orderByDesc('p.status')
            ->orderBy('p.sort')
            ->paginate($this->entity->getPerPage(), ['p.id']);
        $_buttons = [
            '<a href="' . _r('oleus.shop_products.sort') . '" class="uk-button uk-button-medium uk-button-primary uk-button-save-sorting">Сохранить сортировку</a>'
        ];
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
                'style' => 'width: 200px;',
                'data'  => 'Категории',
            ],
            [
                'style' => 'width: 120px;',
                'class' => 'uk-text-small',
                'data'  => 'Модификации',
            ],
            [
                'style' => 'width: 60px;',
                'class' => 'uk-text-small uk-text-center',
                'data'  => 'Цена',
            ],
            [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: sort_by_alpha"></span>',
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
        $_categories = Category::tree_parents();
        if ($_query->isNotEmpty()) {
            $_items = $_query->map(function ($_item) use ($_user, $_categories) {
                $_modifications = '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>';
                $_mod = $_item->_modifications();
                if ($_mod->isNotEmpty()) {
                    $_modifications = $_mod->map(function ($m) use ($_user) {
                        if ($_user->hasPermissionTo($this->permissions['update'])) {
                            return _l($m->title, "oleus.{$this->base_route}.edit", [
                                'p' => [
                                    'id' => $m->id
                                ]
                            ]);
                        } else {
                            return $m->title;
                        }
                    })->implode(', ');
                }
                $_product_categories = '-//-';
                if ($_item->_category->isNotEmpty()) {
                    $_product_categories = $_item->_category->map(function ($_category) use ($_categories) {
                        return _l($_categories->get($_category->id)['title_option'], 'oleus.shop_products', ['p' => ['category' => $_category->id]]);
                    })->implode(', ');
                }
                $_product_price = $_item->_price && $_item->_price->base_price ? view_price($_item->_price->base_price, $_item->_price->base_price) : '-//-';
                $_response = [
                    "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                    $_item->_alias->id ? _l($_item->getTranslation('title', $this->defaultLocale), $_item->_alias->alias, ['attributes' => ['target' => '_blank']]) : $_item->getTranslation('title', $this->defaultLocale),
                    $_product_categories,
                    $_modifications,
                    [
                        'data'  => isset($_product_price['format']) ? "{$_product_price['format']['view_price']} {$_product_price['currency']['suffix']}" : $_product_price,
                        'class' => 'uk-text-right'
                    ],
                    '<input type="number" class="uk-input uk-form-width-xsmall uk-form-small uk-input-number-spin-hide uk-input-sort-item" name="items_sort[' . $_item->id . ']" data-id="' . $_item->id . '" value="' . $_item->sort . '">',
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
        if ($_categories->isNotEmpty()) {
            $_categories = $_categories->map(function ($_item) {
                return $_item['title_option'];
            });
            if ($_categories->isNotEmpty()) {
                $_categories->prepend('-- Без категории --', -1);
                $_categories->prepend('-- Выбрать --', '');
            }
            $_filters[] = [
                'data' => field_render('category', [
                    'value'  => $_filter['category'] ?? NULL,
                    'type'   => 'select',
                    'values' => $_categories,
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
        if ($full_fid = $request->input('full_fid')) {
            $_full_fid = array_shift($full_fid);
            Session::flash('full_fid', json_encode([f_get($_full_fid['id'])]));
        }
        if ($video_preview_fid = $request->input('video_preview_fid')) {
            $_video_preview_fid = array_shift($video_preview_fid);
            Session::flash('video_preview_fid', json_encode([f_get($_video_preview_fid['id'])]));
        }
        if ($video_fid = $request->input('video_fid')) {
            $_video_fid = array_shift($video_fid);
            Session::flash('video_fid', json_encode([f_get($_video_fid['id'])]));
        }
        $this->validate($request, [
            'title' => 'required'
        ], [], [
            'title' => 'Заголовок'
        ]);
        $_save = $request->only([
            'title',
            'sub_title',
            'sku',
            'model',
            'id_1c',
            'brand_id',
            'preview_fid',
            'full_fid',
            'mobile_fid',
            'breadcrumb_title',
            'teaser',
            'body',
            'status',
            'use_spicy',
            'double_card',
            'style_id',
            'style_class',
            'sort',
            'mark_hit',
            'mark_new',
            'mark_recommended_front',
            'mark_recommended_checkout',
            'background_fid',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'meta_robots',
            'video_preview_fid',
            'video_fid',
            'video_youtube',
            'specifications'
        ]);
        $_save['video_preview_fid'] = $_video_preview_fid['id'] ?? NULL;
        $_save['video_fid'] = $_video_fid['video_fid'] ?? NULL;
        $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
        $_save['full_fid'] = $_full_fid['id'] ?? NULL;
        $_save['background_fid'] = $_background_fid['id'] ?? NULL;
        $_save['status'] = (int)($_save['status'] ?? 0);
        $_save['use_spicy'] = (int)($_save['use_spicy'] ?? 0);
        $_save['double_card'] = (int)($_save['double_card'] ?? 0);
        $_save['mark_hit'] = (int)($_save['mark_hit'] ?? 0);
        $_save['mark_new'] = (int)($_save['mark_new'] ?? 0);
        $_save['mark_recommended_checkout'] = (int)($_save['mark_recommended_checkout'] ?? 0);
        $_save['mark_recommended_front'] = (int)($_save['mark_recommended_front'] ?? 0);
        $specifications = collect($request->get('specifications', []));
        if ($specifications->isNotEmpty()) {
            $specifications = $specifications->filter(function ($_item) {
                foreach ($_item as $_data) if ($_data) return TRUE;

                return FALSE;
            });
            $_save['specifications'] = $specifications->values()->toJson();
        }
        $_item = Product::updateOrCreate([
            'id' => NULL
        ], $_save);
        $_item->modify = $_item->id;
        $_item->save();
        $_item->setParamItems();
        $_item->setPrices();
        $_item->setViewLists();
        Session::forget([
            'background_fid',
            'preview_fid',
            'full_fid',
            'video_preview_fid',
            'video_fid',
        ]);

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, Product $_item)
    {
        if ($background_fid = $request->input('background_fid')) {
            $_background_fid = array_shift($background_fid);
            Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
        }
        if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
        }
        if ($full_fid = $request->input('full_fid')) {
            $_full_fid = array_shift($full_fid);
            Session::flash('full_fid', json_encode([f_get($_full_fid['id'])]));
        }
        if ($video_preview_fid = $request->input('video_preview_fid')) {
            $_video_preview_fid = array_shift($video_preview_fid);
            Session::flash('video_preview_fid', json_encode([f_get($_video_preview_fid['id'])]));
        }
        if ($video_fid = $request->input('video_fid')) {
            $_video_fid = array_shift($video_fid);
            Session::flash('video_fid', json_encode([f_get($_video_fid['id'])]));
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
                'specifications',
                'body',
                'meta_title',
                'meta_keywords',
                'meta_description',
            ]);
            $specifications = collect($request->get('specifications', []));
            if ($specifications->isNotEmpty()) {
                $specifications = $specifications->filter(function ($_item) {
                    foreach ($_item as $_data) if ($_data) return TRUE;

                    return FALSE;
                });
                $_save['specifications'] = $specifications->values()->toJson();
            }
            foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
            $_item->save();
        } else {
            $_save = $request->only([
                'title',
                'sub_title',
                'breadcrumb_title',
                'sku',
                'model',
                'id_1c',
                'brand_id',
                'teaser',
                'body',
                'status',
                'use_spicy',
                'double_card',
                'sort',
                'mark_hit',
                'mark_new',
                'mark_recommended_checkout',
                'mark_recommended_front',
                'style_id',
                'style_class',
                'background_fid',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'video_preview_fid',
                'video_fid',
                'video_youtube',
                'specifications',
                'variable_ingredients',
            ]);
            $_save['video_preview_fid'] = $_video_preview_fid['id'] ?? NULL;
            $_save['video_fid'] = $_video_fid['id'] ?? NULL;
            $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
            $_save['full_fid'] = $_full_fid['id'] ?? NULL;
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['use_spicy'] = (int)($_save['use_spicy'] ?? 0);
            $_save['double_card'] = (int)($_save['double_card'] ?? 0);
            $_save['mark_hit'] = (int)($_save['mark_hit'] ?? 0);
            $_save['mark_new'] = (int)($_save['mark_new'] ?? 0);
            $_save['mark_recommended_checkout'] = (int)($_save['mark_recommended_checkout'] ?? 0);
            $_save['mark_recommended_front'] = (int)($_save['mark_recommended_front'] ?? 0);
            $specifications = collect($request->get('specifications', []));
            if ($specifications->isNotEmpty()) {
                $specifications = $specifications->filter(function ($_item) {
                    foreach ($_item as $_data) if ($_data) return TRUE;

                    return FALSE;
                });
                $_save['specifications'] = $specifications->values()->toJson();
            }
            app()->setLocale($_locale);
            $_item->update($_save);
            $_item->setParamItems();
            $_item->setPrices();
            $_item->setViewLists();
        }
        Session::forget([
            'background_fid',
            'preview_fid',
            'full_fid',
            'mobile_fid',
            'video_preview_fid',
            'video_fid',
        ]);

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Product $_item)
    {
        $_item->_category()->detach();
        $_item->_param_items()->detach();
        $_item->_files_related()->detach();
        //        $_item->_files_consist()->detach();
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function categories_selection(Request $request, Product $product)
    {
        $_items_output = NULL;
        if ($_request_categories = $request->get('categories')) {
            $_params = $product->getParamItemsFields($_request_categories);
            if ($_params->isNotEmpty()) {
                $_items_output = $_params->sortByDesc('in_filter')->map(function ($_item) {
                    return $_item['markup'];
                })->implode('');
                $commands['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => 'Теперь Вы можете заполнить информацию о товаре',
                        'status' => 'success',
                    ]
                ];
            } else {
                $commands['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => 'У выбранных категорий нет параметров.',
                        'status' => 'warning',
                    ]
                ];
            }
        } else {
            $commands['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => 'Выберите категорию для товара. Товары<br>без категории не выводятся на сайте.',
                    'status' => 'warning',
                ]
            ];
            $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert> Для заполнения параметров товара выберите категорию к которой он относится.</div>';
        }
        $commands['commands'][] = [
            'command' => 'html',
            'options' => [
                'target' => '#list-category-params-items',
                'data'   => $_items_output
            ]
        ];

        return response($commands, 200);
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

        return response($commands, 200);
    }

    public function related(Request $request, $type, Product $entity, $action, $item = NULL)
    {
        $commands = [];
        switch ($action) {
            case 'add':
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content'     => view('backend.partials.shop.product.related_modal', compact('type', 'entity'))
                            ->render(),
                        'classDialog' => 'uk-width-1-2'
                    ]
                ];
                break;
            case 'save':
                $validate_rules = [
                    'related_product.value'  => 'required_if:related_category.value,',
                    'related_category.value' => 'required_if:related_product.value,'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    'params' => 'Товар'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-product-related-item-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $field = str_replace('.value', '', $field);
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-product-related-item-form'),
                                'data'   => 'uk-form-danger'
                            ]
                        ];
                    }
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => 'Ошибка! Запрос не прошел проверку.<br>Одно из полей должно содержать значение.'
                        ]
                    ];
                } else {

                    if ($_request_value = $request->input('related_product.value')) {
                        $_save[] = [
                            'entity_id'   => $_request_value,
                            'entity_type' => Product::class,
                            'product_id'  => $entity->id,
                            'type'        => $type
                        ];
                    }
                    if ($_request_value = $request->input('related_category.value')) {
                        $_save[] = [
                            'entity_id'   => $_request_value,
                            'entity_type' => Category::class,
                            'product_id'  => $entity->id,
                            'type'        => $type
                        ];
                    }
                    DB::table('shop_product_related')
                        ->insert($_save);
                    $_items = $entity->_product_related('related', TRUE);
                    $_items_output = view('backend.partials.shop.product.related_item', compact('_items', 'entity', 'type'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => "#list-product-{$type}-select-items",
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
                DB::table('shop_product_related')
                    ->where('id', $item)
                    ->delete();
                $_items = $entity->_product_related('related', TRUE);
                if ($_items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.product.related_item', compact('_items', 'entity', 'type'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => "#list-product-{$type}-select-items",
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

        return response($commands, 200);
    }

    public function related_entity(Request $request, $type, $entity_type, Product $entity)
    {
        $_response = [];
        if ($_search = $request->input('search')) {
            if ($entity_type == 'product') {
                $_related_entities = DB::table('shop_product_related as pr')
                    ->where('pr.product_id', $entity->id)
                    ->where('pr.entity_type', Product::class)
                    ->where('pr.type', $type)
                    ->distinct()
                    ->pluck('entity_id');
                $_products = UrlAlias::where('model_type', Product::class)
                    ->where('model_default_title', 'like', "%{$_search}%")
                    ->where('model_id', '<>', $entity->id);
            } else {
                $_related_entities = DB::table('shop_product_related as pr')
                    ->where('pr.product_id', $entity->id)
                    ->where('pr.entity_type', Category::class)
                    ->where('pr.type', $type)
                    ->distinct()
                    ->pluck('entity_id');
                $_products = UrlAlias::where('model_type', Category::class)
                    ->where('model_default_title', 'like', "%{$_search}%")
                    ->where('model_id', '<>', $entity->id);
            }
            if ($_related_entities->isNotEmpty()) $_products->whereNotIn('model_id', $_related_entities);
            $_products = $_products->orderByRaw("CASE WHEN (model_default_title LIKE '{$_search}%') THEN 0 WHEN (model_default_title LIKE '%{$_search}%') THEN 1 ELSE 2 END")
                ->orderBy('model_id')
                ->limit(10)
                ->get([
                    'model_id as id',
                    'model_default_title as title'
                ]);
            if ($_products->isNotEmpty()) {
                $_products->each(function ($_item) use (&$_response) {
                    $_response[] = [
                        'name' => "{$_item->id}::{$_item->title}",
                        'view' => NULL,
                        'data' => $_item->id
                    ];
                });
            }
        }

        return response($_response, 200);
    }


    public function consist(Request $request, Product $entity, $action, $item = NULL)
    {
        $commands = [];
        switch ($action) {
            case 'add':
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content'     => view('backend.partials.shop.product.consists_modal', compact('entity'))
                            ->render(),
                        'classDialog' => 'uk-width-1-2'
                    ]
                ];
                break;
            case 'save':
                $validate_rules = [
                    'consist_product.value' => 'required',
                    'quantity'              => 'required|integer|min:1',
                    //                    'consist_category.value' => 'required_if:consist_product.value,'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    'consist_product.value' => 'Товар',
                    'quantity'              => 'Количество в составе'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-product-consist-item-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $field = str_replace('.value', '', $field);
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-product-consist-item-form'),
                                'data'   => 'uk-form-danger'
                            ]
                        ];
                    }
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'danger',
                            'text'   => 'Ошибка! Запрос не прошел проверку.<br>Одно из полей должно содержать значение.'
                        ]
                    ];
                } else {
                    $_save['quantity'] = $request->get('quantity');
                    if ($_request_value = $request->input('consist_product.value')) {
                        $_save = array_merge($_save, [
                            'entity_id'   => $_request_value,
                            'entity_type' => Product::class,
                            'product_id'  => $entity->id,
                        ]);
                    }
                    //                    if ($_request_value = $request->input('consist_category.value')) {
                    //                        $_save[] = [
                    //                            'entity_id'   => $_request_value,
                    //                            'entity_type' => Category::class,
                    //                            'product_id'  => $entity->id,
                    //
                    //                        ];
                    //                    }
                    DB::table('shop_product_consists')
                        ->insert($_save);
                    $_items = $entity->_product_consist('consist', TRUE);
                    $_items_output = view('backend.partials.shop.product.consists_item', compact('_items', 'entity'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => "#list-product-consists-select-items",
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
                DB::table('shop_product_consists')
                    ->where('id', $item)
                    ->delete();
                $_items = $entity->_product_consist('consists', TRUE);
                if ($_items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.product.consists_item', compact('_items', 'entity'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => "#list-product-consists-select-items",
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

        return response($commands, 200);
    }

    public function consist_entity(Request $request, Product $entity)
    {
        $_response = [];
        if ($_search = $request->input('search')) {
            //            if ($entity_type == 'product') {
            $_consist_entities = DB::table('shop_product_consists as pr')
                ->where('pr.product_id', $entity->id)
                ->distinct()
                ->pluck('entity_id');
            $_products = UrlAlias::where('model_type', Product::class)
                ->where('model_default_title', 'like', "%{$_search}%")
                ->where('model_id', '<>', $entity->id);
            //            } else {
            //                $_consist_entities = DB::table('shop_product_consists as pr')
            //                    ->where('pr.product_id', $entity->id)
            //                    ->where('pr.entity_type', Category::class)
            //                    ->where('pr.type', 'consist')
            //                    ->distinct()
            //                    ->pluck('entity_id');
            //                $_products = UrlAlias::where('model_type', Category::class)
            //                    ->where('model_default_title', 'like', "%{$_search}%")
            //                    ->where('model_id', '<>', $entity->id);
            //            }
            if ($_consist_entities->isNotEmpty()) $_products->whereNotIn('model_id', $_consist_entities);
            $_products = $_products->orderByRaw("CASE WHEN (model_default_title LIKE '{$_search}%') THEN 0 WHEN (model_default_title LIKE '%{$_search}%') THEN 1 ELSE 2 END")
                ->orderBy('model_id')
                ->limit(10)
                ->get([
                    'model_id as id',
                    'model_default_title as title'
                ]);
            if ($_products->isNotEmpty()) {
                $_products->each(function ($_item) use (&$_response) {
                    $_response[] = [
                        'name' => "{$_item->id}::{$_item->title}",
                        'view' => NULL,
                        'data' => $_item->id
                    ];
                });
            }
        }

        return response($_response, 200);
    }


    public function save_sort(Request $request)
    {
        $_sorting = $request->all();
        foreach ($_sorting as $_id => $_sort) {
            Product::where('id', $_id)
                ->update([
                    'sort' => $_sort
                ]);
        }

        $commands['commands'][] = [
            'command' => 'UK_notification',
            'options' => [
                'status' => 'success',
                'text'   => 'Сортировка товаров сохранена. Обновите страницу'
            ]
        ];

        return response($commands, 200);
    }

    public function modify(Request $request, Product $product, $action, $item = NULL)
    {
        if (!$item) $item = new Product();
        $commands = [];
        switch ($action) {
            case 'add':
            case 'edit':
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content' => view('backend.partials.shop.product.modifications_modal', compact('item', 'product'))
                            ->render(),
                    ]
                ];
                break;
            case 'save':
            case 'update':
                $_save = $request->input('item');
                $validate_rules = [
                    'item.exists'    => 'required_if:item.type,exists',
                    'item.new_title' => 'required_if:item.type,new'
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
                            'text'   => 'Ошибка сохранения. Заполните обязательные поля'
                        ]
                    ];
                } else {
                    if ($_save['type'] == 'exists') {
                        Product::where('id', $_save['exists'])
                            ->update([
                                'modify' => $_save['product_id']
                            ]);
                    } else {
                        $product->dupl($_save['new_title']);
                    }
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-additionally-ingredients-select-items',
                            'data'   => view('backend.partials.shop.product.modifications_table', [
                                '_items'  => $product->_modifications(),
                                'product' => $product
                            ])
                                ->render()
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'status' => 'success',
                            'text'   => 'Модификация сохранена'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_modalClose'
                    ];
                }
                break;
            case 'destroy':
                $item->update([
                    'modify' => $item->id
                ]);
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-additionally-ingredients-select-items',
                        'data'   => view('backend.partials.shop.product.modifications_table', [
                            '_items'  => $product->_modifications(),
                            'product' => $product
                        ])
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
