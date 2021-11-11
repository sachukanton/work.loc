<?php

// Dashboard
Route::get('/', [
    'as'   => 'oleus',
    'uses' => 'DashboardController@dashboard'
]);

// Polygon
Route::get('/polygon', [
    'as'   => 'oleus.polygon',
    'uses' => 'DashboardController@polygon'
]);

// Users
Route::resource('/users', 'User\UserController', [
    'names'  => [
        'index'   => 'oleus.users',
        'create'  => 'oleus.users.create',
        'update'  => 'oleus.users.update',
        'store'   => 'oleus.users.store',
        'edit'    => 'oleus.users.edit',
        'destroy' => 'oleus.users.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/users/export', [
    'as'   => 'oleus.users.export',
    'uses' => 'User\UserController@export'
]);

// Roles
Route::resource('/roles', 'User\RoleController', [
    'names'  => [
        'index'   => 'oleus.roles',
        'create'  => 'oleus.roles.create',
        'update'  => 'oleus.roles.update',
        'store'   => 'oleus.roles.store',
        'edit'    => 'oleus.roles.edit',
        'destroy' => 'oleus.roles.destroy'
    ],
    'except' => [
        'show'
    ]
]);

// Pages
Route::resource('/pages', 'Structure\PageController', [
    'names'  => [
        'index'   => 'oleus.pages',
        'create'  => 'oleus.pages.create',
        'update'  => 'oleus.pages.update',
        'store'   => 'oleus.pages.store',
        'edit'    => 'oleus.pages.edit',
        'destroy' => 'oleus.pages.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/pages/{page}/{locale}', [
    'as'   => 'oleus.pages.translate',
    'uses' => 'Structure\PageController@translate'
]);

// Nodes
Route::resource('/nodes', 'Structure\NodeController', [
    'names'  => [
        'index'   => 'oleus.nodes',
        'create'  => 'oleus.nodes.create',
        'update'  => 'oleus.nodes.update',
        'store'   => 'oleus.nodes.store',
        'edit'    => 'oleus.nodes.edit',
        'destroy' => 'oleus.nodes.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/nodes/{node}/{locale}', [
    'as'   => 'oleus.nodes.translate',
    'uses' => 'Structure\NodeController@translate'
]);

// Tags
Route::resource('/tags', 'Structure\TagController', [
    'names'  => [
        'index'   => 'oleus.tags',
        'create'  => 'oleus.tags.create',
        'update'  => 'oleus.tags.update',
        'store'   => 'oleus.tags.store',
        'edit'    => 'oleus.tags.edit',
        'destroy' => 'oleus.tags.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/tags/{tag}/{locale}', [
    'as'   => 'oleus.tags.translate',
    'uses' => 'Structure\TagController@translate'
]);

// Variables
Route::resource('/variables', 'Component\VariablesController', [
    'names'  => [
        'index'   => 'oleus.variables',
        'create'  => 'oleus.variables.create',
        'edit'    => 'oleus.variables.edit',
        'update'  => 'oleus.variables.update',
        'store'   => 'oleus.variables.store',
        'destroy' => 'oleus.variables.destroy',
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/variables/{variable}/{locale}', [
    'as'   => 'oleus.variables.translate',
    'uses' => 'Component\VariablesController@translate'
]);

// Forms
Route::resource('/forms', 'Form\FormController', [
    'names'  => [
        'index'   => 'oleus.forms',
        'create'  => 'oleus.forms.create',
        'store'   => 'oleus.forms.store',
        'edit'    => 'oleus.forms.edit',
        'update'  => 'oleus.forms.update',
        'destroy' => 'oleus.forms.destroy',
    ],
    'except' => [
        'show',
    ]
]);
Route::get('/forms/{form}/{locale}', [
    'as'   => 'oleus.forms.translate',
    'uses' => 'Form\FormController@translate'
]);
Route::match([
    'delete',
    'post'
], '/forms/field/{form}/{action}/{key?}', [
    'as'   => 'oleus.forms.field',
    'uses' => 'Form\FormController@field'
]);

// FormsData
Route::resource('/forms-data', 'Form\FormDataController', [
    'names'  => [
        'index'   => 'oleus.forms_data',
        'edit'    => 'oleus.forms_data.edit',
        'update'  => 'oleus.forms_data.update',
        'destroy' => 'oleus.forms_data.destroy',
    ],
    'except' => [
        'show',
        'create',
        'store',
    ]
]);

// Blocks
Route::resource('/blocks', 'Component\BlockController', [
    'names'  => [
        'index'   => 'oleus.blocks',
        'create'  => 'oleus.blocks.create',
        'update'  => 'oleus.blocks.update',
        'store'   => 'oleus.blocks.store',
        'edit'    => 'oleus.blocks.edit',
        'destroy' => 'oleus.blocks.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/blocks/{block}/{locale}', [
    'as'   => 'oleus.blocks.translate',
    'uses' => 'Component\BlockController@translate'
]);

// Banner
Route::resource('/banners', 'Component\BannerController', [
    'names'  => [
        'index'   => 'oleus.banners',
        'create'  => 'oleus.banners.create',
        'update'  => 'oleus.banners.update',
        'store'   => 'oleus.banners.store',
        'edit'    => 'oleus.banners.edit',
        'destroy' => 'oleus.banners.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/banners/{banner}/{locale}', [
    'as'   => 'oleus.banners.translate',
    'as'   => 'oleus.banners.translate',
    'uses' => 'Component\BannerController@translate'
]);

// Advantages
Route::resource('/advantages', 'Component\AdvantageController', [
    'names'  => [
        'index'   => 'oleus.advantages',
        'create'  => 'oleus.advantages.create',
        'update'  => 'oleus.advantages.update',
        'store'   => 'oleus.advantages.store',
        'edit'    => 'oleus.advantages.edit',
        'destroy' => 'oleus.advantages.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/advantages/{advantage}/{locale}', [
    'as'   => 'oleus.advantages.translate',
    'uses' => 'Component\AdvantageController@translate'
]);
Route::match([
    'delete',
    'post'
], '/advantages/item/{advantage}/{action}/{id?}', [
    'as'   => 'oleus.advantages.item',
    'uses' => 'Component\AdvantageController@item'
]);
Route::post('/advantages/{advantage}/sort', [
    'as'   => 'oleus.advantages.sort',
    'uses' => 'Component\AdvantageController@save_sort'
]);

// Sliders
Route::resource('/sliders', 'Component\SliderController', [
    'names'  => [
        'index'   => 'oleus.sliders',
        'create'  => 'oleus.sliders.create',
        'update'  => 'oleus.sliders.update',
        'store'   => 'oleus.sliders.store',
        'edit'    => 'oleus.sliders.edit',
        'destroy' => 'oleus.sliders.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::match([
    'delete',
    'post'
], '/callback/sliders/item/{slider}/{action}/{id?}', [
    'as'   => 'oleus.sliders.item',
    'uses' => 'Component\SliderController@item'
]);
Route::post('/sliders/{slider}/sort', [
    'as'   => 'oleus.sliders.sort',
    'uses' => 'Component\SliderController@save_sort'
]);

// Faq
Route::resource('/faqs', 'Structure\FaqController', [
    'names'  => [
        'index'   => 'oleus.faqs',
        'create'  => 'oleus.faqs.create',
        'update'  => 'oleus.faqs.update',
        'store'   => 'oleus.faqs.store',
        'edit'    => 'oleus.faqs.edit',
        'destroy' => 'oleus.faqs.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/faqs/{faq}/{locale}', [
    'as'   => 'oleus.faqs.translate',
    'uses' => 'Structure\FaqController@translate'
]);

// Menus
Route::resource('/menus', 'Component\MenuController', [
    'names'  => [
        'index'   => 'oleus.menus',
        'create'  => 'oleus.menus.create',
        'edit'    => 'oleus.menus.edit',
        'update'  => 'oleus.menus.update',
        'store'   => 'oleus.menus.store',
        'destroy' => 'oleus.menus.destroy',
    ],
    'except' => [
        'show'
    ]
]);
Route::post('/menus/search_link', [
    'as'   => 'oleus.menus.link',
    'uses' => 'Component\MenuController@search_link'
]);
Route::match([
    'delete',
    'post'
], '/menus/item/{menu}/{action}/{id?}', [
    'as'   => 'oleus.menus.item',
    'uses' => 'Component\MenuController@item'
]);
Route::post('/menus/{menu}/sort', [
    'as'   => 'oleus.menus.sort',
    'uses' => 'Component\MenuController@save_sort'
]);

// Settings
Route::match([
    'get',
    'post'
], '/settings/{setting}', [
    'as'   => 'oleus.settings',
    'uses' => 'SettingsController@_view'
]);
Route::post('/settings/translate/{setting}/{action}', [
    'as'   => 'oleus.settings.translate',
    'uses' => 'SettingsController@_translate'
]);

// Artisan
Route::get('/artisan/{command}/{target}', [
    'as'   => 'oleus.artisan',
    'uses' => 'DashboardController@artisan'
]);

// Shop Brands
Route::resource('/shop-brands', 'Shop\BrandController', [
    'names'  => [
        'index'   => 'oleus.shop_brands',
        'create'  => 'oleus.shop_brands.create',
        'update'  => 'oleus.shop_brands.update',
        'store'   => 'oleus.shop_brands.store',
        'edit'    => 'oleus.shop_brands.edit',
        'destroy' => 'oleus.shop_brands.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/shop-brands/{shop_brand}/{locale}', [
    'as'   => 'oleus.shop_brands.translate',
    'uses' => 'Shop\BrandController@translate'
]);

// Shop Param
Route::resource('/shop-params', 'Shop\ParamController', [
    'names'  => [
        'index'   => 'oleus.shop_params',
        'create'  => 'oleus.shop_params.create',
        'update'  => 'oleus.shop_params.update',
        'store'   => 'oleus.shop_params.store',
        'edit'    => 'oleus.shop_params.edit',
        'destroy' => 'oleus.shop_params.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/shop-params/{shop_param}/{locale}', [
    'as'   => 'oleus.shop_params.translate',
    'uses' => 'Shop\ParamController@translate'
]);
Route::match([
    'delete',
    'post'
], 'shop-params/item/{shop_param}/{action}/{shop_param_item?}', [
    'as'   => 'oleus.shop_params.item',
    'uses' => 'Shop\ParamController@item'
]);

Route::post('callback/shop-params/alias', [
    'as'   => 'oleus.shop_params.alias',
    'uses' => 'Shop\ParamController@alias'
]);

//Route::post('/shop-product-list/product', [
//    'as'   => 'oleus.shop_product_list.product',
//    'uses' => 'Shop\ListController@product'
//]);

// Shop Category
Route::resource('/shop-categories', 'Shop\CategoryController', [
    'names'  => [
        'index'   => 'oleus.shop_categories',
        'create'  => 'oleus.shop_categories.create',
        'update'  => 'oleus.shop_categories.update',
        'store'   => 'oleus.shop_categories.store',
        'edit'    => 'oleus.shop_categories.edit',
        'destroy' => 'oleus.shop_categories.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/shop-categories/{shop_category}/{locale}', [
    'as'   => 'oleus.shop_categories.translate',
    'uses' => 'Shop\CategoryController@translate'
]);
Route::match([
    'delete',
    'post'
], 'shop-categories/param/{shop_category}/{action}/{shop_param?}', [
    'as'   => 'oleus.shop_categories.param',
    'uses' => 'Shop\CategoryController@param'
]);
Route::match([
    'delete',
    'post'
], 'shop-categories/banner/{shop_category}/{action}/{banner?}', [
    'as'   => 'oleus.shop_categories.banner',
    'uses' => 'Shop\CategoryController@banner'
]);
Route::match([
    'delete',
    'post'
], 'callback/shop-categories/additional-item/{category}/{action}/{additional_item?}', [
    'as'   => 'oleus.shop_categories.additional_item',
    'uses' => 'Shop\CategoryController@additional_item'
]);

// Shop Filter Page
Route::resource('/shop-filter-pages', 'Shop\FilterPageController', [
    'names'  => [
        'index'   => 'oleus.shop_filter_pages',
        'create'  => 'oleus.shop_filter_pages.create',
        'update'  => 'oleus.shop_filter_pages.update',
        'store'   => 'oleus.shop_filter_pages.store',
        'edit'    => 'oleus.shop_filter_pages.edit',
        'destroy' => 'oleus.shop_filter_pages.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/shop-filter-pages/{shop_filter_page}/{locale}', [
    'as'   => 'oleus.shop_filter_pages.translate',
    'uses' => 'Shop\FilterPageController@translate'
]);
Route::match([
    'delete',
    'post'
], 'shop-filter-pages/page/{shop_filter_page}/{action}/{shop_filter_page_2?}', [
    'as'   => 'oleus.shop_filter_pages.page',
    'uses' => 'Shop\FilterPageController@page'
]);

// Shop Product
Route::resource('/shop-products', 'Shop\ProductController', [
    'names'  => [
        'index'   => 'oleus.shop_products',
        'create'  => 'oleus.shop_products.create',
        'update'  => 'oleus.shop_products.update',
        'store'   => 'oleus.shop_products.store',
        'edit'    => 'oleus.shop_products.edit',
        'destroy' => 'oleus.shop_products.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/shop-products/{shop_product}/{locale}', [
    'as'   => 'oleus.shop_products.translate',
    'uses' => 'Shop\ProductController@translate'
]);
Route::post('/shop-products/categories-selection/{shop_product?}', [
    'as'   => 'oleus.shop_products.categories_selection',
    'uses' => 'Shop\ProductController@categories_selection'
]);
Route::match([
    'delete',
    'post'
], 'callback/shop-products/modify/{shop_product}/{action}/{shop_product_modify?}', [
    'as'   => 'oleus.shop_products.modify',
    'uses' => 'Shop\ProductController@modify'
]);


Route::match([
    'delete',
    'post'
], 'shop-products/{type}/{shop_product}/{action}/{item?}', [
    'as'   => 'oleus.shop_products.related',
    'uses' => 'Shop\ProductController@related'
]);

Route::post('/shop-products/autocomplete/{type}/{entity_type}/{shop_product}', [
    'as'   => 'oleus.shop_products.related_entity',
    'uses' => 'Shop\ProductController@related_entity'
]);


Route::match([
    'delete',
    'post'
], 'shop-products/{shop_product}/{action}/{item?}', [
    'as'   => 'oleus.shop_products.consist',
    'uses' => 'Shop\ProductController@consist'
]);
Route::post('/shop-products/autocomplete/{shop_product}', [
    'as'   => 'oleus.shop_products.consist_entity',
    'uses' => 'Shop\ProductController@consist_entity'
]);


Route::post('/shop-products/sort', [
    'as'   => 'oleus.shop_products.sort',
    'uses' => 'Shop\ProductController@save_sort'
]);

// Shop Form
Route::resource('/shop-forms-data', 'Shop\FormDataController', [
    'names'  => [
        'index'   => 'oleus.shop_forms_data',
        'update'  => 'oleus.shop_forms_data.update',
        'edit'    => 'oleus.shop_forms_data.edit',
        'destroy' => 'oleus.shop_forms_data.destroy'
    ],
    'except' => [
        'show',
        'create',
        'store'
    ]
]);

// Shop Order
Route::resource('/shop-orders', 'Shop\OrderController', [
    'names'  => [
        'index'   => 'oleus.shop_orders',
        'update'  => 'oleus.shop_orders.update',
        'edit'    => 'oleus.shop_orders.edit',
        'destroy' => 'oleus.shop_orders.destroy'
    ],
    'except' => [
        'show',
        'create',
        'store'
    ]
]);
Route::get('/shop-orders/download/{shop_order}', [
    'as'   => 'oleus.shop_orders.download',
    'uses' => 'Shop\OrderController@download'
]);
Route::post('/shop-orders/view-order', [
    'as'   => 'oleus.shop_orders.view_order',
    'uses' => 'Shop\OrderController@view_order'
]);
Route::post('/shop-orders/save-order', [
    'as'   => 'oleus.shop_orders.save_order',
    'uses' => 'Shop\OrderController@save_order'
]);
Route::post('order-lists-update', [
    'as'   => 'oleus.control.order_lists_update',
    'uses' => 'Shop\OrderController@order_lists_update'
]);

// Shop Product list
Route::resource('/shop-product-list', 'Shop\ListController', [
    'names'  => [
        'index'   => 'oleus.shop_product_list',
        'create'  => 'oleus.shop_product_list.create',
        'update'  => 'oleus.shop_product_list.update',
        'store'   => 'oleus.shop_product_list.store',
        'edit'    => 'oleus.shop_product_list.edit',
        'destroy' => 'oleus.shop_product_list.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::post('/shop-product-list/product', [
    'as'   => 'oleus.shop_product_list.product',
    'uses' => 'Shop\ListController@product'
]);

// Redirect
Route::get('/redirects', [
    'uses' => 'Seo\RedirectController@index',
    'as'   => 'oleus.redirects'
]);
Route::match([
    'delete',
    'post'
], '/redirects/{action}/{redirect?}', [
    'as'   => 'oleus.redirects.item',
    'uses' => 'Seo\RedirectController@item'
]);
Route::post('/redirects/link', [
    'as'   => 'oleus.redirects.link',
    'uses' => 'Seo\RedirectController@link'
]);

// Journal
Route::get('/journal', [
    'uses' => 'JournalController@index',
    'as'   => 'oleus.journal'
]);

// Modal Banner
Route::resource('/modal-banners', 'Component\ModalBannerController', [
    'names'  => [
        'index'   => 'oleus.modal_banners',
        'create'  => 'oleus.modal_banners.create',
        'update'  => 'oleus.modal_banners.update',
        'store'   => 'oleus.modal_banners.store',
        'edit'    => 'oleus.modal_banners.edit',
        'destroy' => 'oleus.modal_banners.destroy'
    ],
    'except' => [
        'show'
    ]
]);
Route::get('/modal-banners/{modal_banner}/{locale}', [
    'as'   => 'oleus.modal_banners.translate',
    'as'   => 'oleus.modal_banners.translate',
    'uses' => 'Component\ModalBannerController@translate'
]);

// Gifts
Route::resource('/shop-gifts', 'Shop\GiftController', [
    'names'  => [
        'index'   => 'oleus.shop_gifts',
        'create'  => 'oleus.shop_gifts.create',
        'update'  => 'oleus.shop_gifts.update',
        'store'   => 'oleus.shop_gifts.store',
        'edit'    => 'oleus.shop_gifts.edit',
    ],
    'except' => [
        'show',
        'destroy'
    ]
]);
Route::post('/shop-gifts/product', [
    'as'   => 'oleus.shop_gifts.product',
    'uses' => 'Shop\GiftController@product'
]);
