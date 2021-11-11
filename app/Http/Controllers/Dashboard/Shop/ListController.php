<?php

    namespace App\Http\Controllers\Dashboard\Shop;

    use App\Library\BaseController;
    use App\Models\Seo\UrlAlias;
    use App\Models\Shop\Category;
    use App\Models\Shop\Product;
    use App\Models\Shop\ViewList;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class ListController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'     => 'Товары в списках',
                'create'    => 'Добавить товар в список',
                'edit'      => 'Редактировать товар в списках',
                'translate' => '',
                'delete'    => '',
            ];
            $this->middleware([
                'permission:shop_products_read'
            ]);
            $this->base_route = 'shop_product_list';
            $this->permissions = [
                'read'   => 'shop_products_read',
                'create' => 'shop_products_create',
                'update' => 'shop_products_update',
                'delete' => 'shop_products_delete'
            ];
            $this->entity = new ViewList();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->use_multi_language = FALSE;
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_selected_lists = [];
            $_discount_price = NULL;
            if ($entity->new) $_selected_lists[] = 'new';
            if ($entity->hit) $_selected_lists[] = 'hit';
            if ($entity->discount) $_selected_lists[] = 'discount';
            if ($entity->recommended_front) $_selected_lists[] = 'recommended_front';
            if ($entity->recommended_checkout) $_selected_lists[] = 'recommended_checkout';
            if ($entity->_product->exists) {
                $_location = NULL;
                $_prices = $entity->_product->_prices->keyBy('location');
                $_price = $_prices->get($_location);
                $_discount_price = $_price->discount_price;
            }
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        field_render('product', [
                            'type'       => 'autocomplete',
                            'label'      => 'Товар',
                            'value'      => $entity->_product->id,
                            'selected'   => $entity->_product->getTranslation('title', $this->defaultLocale),
                            'class'      => 'uk-autocomplete',
                            'attributes' => [
                                'data-url'   => _r('oleus.shop_product_list.product'),
                                'data-value' => 'name'
                            ],
                            'required'   => TRUE,
                            'help'       => 'Начните вводить название товара'
                        ]),
                        field_render('list', [
                            'value'    => $_filter['list'] ?? NULL,
                            'label'    => 'Список товаров',
                            'type'     => 'select',
                            'multiple' => TRUE,
                            'required' => TRUE,
                            'values'   => $this->entity::PRODUCT_VIEW_LIST,
                            'selected' => $_selected_lists,
                            'class'    => 'uk-select2',
                        ]),
                        field_render("discount_price", [
                            'type'       => 'number',
                            'label'      => 'Акционная цена',
                            'value'      => $_discount_price,
                            'attributes' => [
                                'min'  => 0,
                                'step' => 0.01
                            ],
                            'help'       => 'Поле обязательно к заполнению, если указано что, товар есть в списке "Акционные товары"'
                        ]),
                        '<hr class="uk-divider-icon"><div class="uk-alert uk-alert-warning"><strong>Внимание!!!</strong><br>Данные из этой формы напрямую связаны с товаром. При их изменении, меняются данные в самой карточке товара.</div>'
                    ],
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
            $_items = collect([]);
            $_user = Auth::user();
            $_query = Product::from('shop_products as p')
                ->leftJoin('url_alias as a', 'a.model_id', '=', 'p.id')
                ->leftJoin('shop_product_category as c', 'c.model_id', '=', 'p.id')
                ->join('shop_product_lists as pl', 'pl.product_id', '=', 'p.id')
                ->when($_filter, function ($query) use ($_filter) {
                    if (isset($_filter['category']) && $_filter['category']) {
                        $_query_categories = Category::find($_filter['category']);
                        $_query_categories_children = $_query_categories->all_children;
                        $_query_categories_children->put($_query_categories->id, $_query_categories);
                        $query->whereIn('c.category_id', $_query_categories_children->pluck('id'));
                    }
                    if (isset($_filter['list']) && $_filter['list'] != 'all') $query->where("pl.{$_filter['list']}", 1);
                    if (isset($_filter['title']) && $_filter['title']) $query->where('a.model_default_title', 'like', "%{$_filter['title']}%");
                    if (isset($_filter['alias']) && $_filter['alias']) {
                        $query->where('a.model_type', '=', Product::class)
                            ->where('a.alias', 'like', "%{$_filter['alias']}%");
                    }
                })
                ->orderByDesc('p.status')
                ->orderBy('p.id')
                ->distinct()
                ->select([
                    'p.id',
                    'p.title',
                    'p.mark_hit',
                    'p.mark_new',
                    'p.status',
                    'pl.new',
                    'pl.hit',
                    'pl.discount',
                    'pl.recommended_front',
                    'pl.recommended_checkout',
                    'pl.id as view_id',
                ])
                ->with([
                    '_alias',
                    '_category',
                ])
                ->paginate($this->entity->getPerPage(), ['p.id']);
            $_buttons = [];
            if ($_user->hasPermissionTo($this->permissions['create'])) {
                $_buttons[] = _l('Добавить товар в список', "oleus.{$this->base_route}.create", [
                    'attributes' => [
                        'class' => 'uk-button uk-button-success uk-text-uppercase'
                    ]
                ]);
            }
            $_headers = [
                [
                    'data' => 'Товар',
                ],
                [
                    'style' => 'width: 450px;',
                    'class' => 'uk-text-small',
                    'data'  => 'Категория',
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => 'Списки',
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
                    $_product_categories = '-//-';
                    if ($_item->_category->isNotEmpty()) {
                        $_product_categories = $_item->_category->map(function ($_category) use ($_categories) {
                            return _l($_categories->get($_category->id)['title_option'], 'oleus.shop_products', ['p' => ['category' => $_category->id]]);
                        })->implode(', ');
                    }
                    $_lists = [];
                    if ($_item->new) $_lists[] = trans(ViewList::PRODUCT_VIEW_LIST['new']);
                    if ($_item->hit) $_lists[] = trans(ViewList::PRODUCT_VIEW_LIST['hit']);
                    if ($_item->discount) $_lists[] = trans(ViewList::PRODUCT_VIEW_LIST['discount']);
                    if ($_item->recommended_front) $_lists[] = trans(ViewList::PRODUCT_VIEW_LIST['recommended_front']);
                    if ($_item->recommended_checkout) $_lists[] = trans(ViewList::PRODUCT_VIEW_LIST['recommended_checkout']);
                    $_response = [
                        $_item->_alias->id ? _l($_item->getTranslation('title', $this->defaultLocale), $_item->_alias->alias, ['attributes' => ['target' => '_blank']]) : $_item->getTranslation('title', $this->defaultLocale),
                        $_product_categories,
                        implode(', ', $_lists),
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_response[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->view_id
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
                if ($_categories->isNotEmpty()) $_categories->prepend('-- Выбрать --', '');
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
                'data' => field_render('list', [
                    'value'  => $_filter['list'] ?? NULL,
                    'type'   => 'select',
                    'values' => array_merge(['all' => '- выбрать -'], $this->entity::PRODUCT_VIEW_LIST),
                    'class'  => 'uk-select2',
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
            $this->validate($request, [
                'product.value'  => 'required',
                'list'           => 'required',
                'discount_price' => 'multiRequiredIf:list,discount'
            ], [], [
                'product.value'  => 'Товар',
                'list'           => 'Список товаров',
                'discount_price' => 'Акционная цена',
            ]);
            $_lists = $request->get('list');
            $_product = Product::find($request->input('product.value'));
            $_mark = [
                'hit'                  => 0,
                'new'                  => 0,
                'discount'             => 0,
                'recommended_front'    => 0,
                'recommended_checkout' => 0,
            ];
            foreach ($_lists as $_list) $_mark[$_list] = 1;
            $_item = $this->entity::updateOrCreate([
                'product_id' => $request->input('product.value'),
            ], array_merge($_mark, [
                'product_id' => $_product->id
            ]));
            $_item->updateDataProduct();

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, ViewList $_item)
        {
            $this->validate($request, [
                'product.value'  => 'required',
                'list'           => 'required',
                'discount_price' => 'multiRequiredIf:list,discount'
            ], [], [
                'product.value'  => 'Товар',
                'list'           => 'Список товаров',
                'discount_price' => 'Акционная цена',
            ]);
            $_lists = $request->get('list');
            $_request_product = Product::find($request->input('product.value'));
            $_mark = [
                'hit'                  => 0,
                'new'                  => 0,
                'recommended_front'    => 0,
                'recommended_checkout' => 0,
            ];
            foreach ($_lists as $_list) $_mark[$_list] = 1;
            if ($_item->product_id != $_request_product->id) {
                $_item->clearDataProduct();
                $_item->product_id = $_request_product->id;
            }
            $_item->update($_mark);
            $_item->updateDataProduct($_item->product_id);

            return $this->__response_after_update($request, $_item);
        }

        public function destroy(Request $request, ViewList $_item)
        {
            $_item->clearDataProduct();
            $_item->delete();

            return $this->__response_after_destroy($request, $_item);
        }

        public function product(Request $request)
        {
            $_items = [];
            if ($_search = $request->input('search')) {
                $_exists_id = $this->entity::pluck('product_id');
                $_items = UrlAlias::from('url_alias as a')
                    ->with([
                        'model'
                    ])
                    ->where('a.model_default_title', 'like', "%{$_search}%")
                    ->where('a.model_type', Product::class)
                    ->when($_exists_id, function ($query) use ($_exists_id) {
                        $query->whereNotIn('a.model_id', $_exists_id);
                    })
                    ->limit(8)
                    ->get([
                        'a.*',
                    ]);
                if ($_items->isNotEmpty()) {
                    $_items = $_items->transform(function ($_item) {
                        $_model = $_item->model;

                        return [
                            'name' => $_model->title,
                            'view' => NULL,
                            'data' => $_model->id
                        ];

                    })->toArray();
                }
            }

            return response($_items, 200);
        }

    }
