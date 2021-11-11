<?php

return [
    'styles'  => [
        [
            'url' => '//fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:300,400,500,700,900&amp;subset=cyrillic',
        ],
        [
            'url' => 'css/uikit.css'
        ],
        [
            'url' => 'css/codemirror.css'
        ]
    ],
    'scripts' => [
        [
            'url' => 'js/header_part.js',
        ],
        [
            'url'      => 'library/ckeditor_sdk/ckeditor/ckeditor.js',
            'position' => 'footer'
        ],
        [
            'url'      => 'js/codemirror.js',
            'position' => 'footer'
        ],
        [
            'url'      => 'js/footer_part.js',
            'position' => 'footer'
        ],
        [
            'url'      => 'template/js/script-phonemask.js',
            'position' => 'footer',
            'sort'     => 1001
        ]
    ],
    'menu'    => [
        [
            'link'       => 'Главная',
            'route'      => 'oleus',
            'icon'       => 'dashboard',
            'permission' => 'access_dashboard'
        ],
        //        [
        //            'link'       => 'Пользователи',
        //            'icon'       => 'person',
        //            'permission' => [
        //                'roles_read',
        //                'users_read',
        //            ],
        //            'children'   => [
        //                [
        //                    'link'       => 'Пользователи',
        //                    'route'      => 'oleus.users',
        //                    'permission' => 'users_read'
        //                ],
        //                [
        //                    'link'       => 'Роли пользователей',
        //                    'route'      => 'oleus.roles',
        //                    'permission' => 'roles_read'
        //                ],
        //            ]
        //        ],
        [
            'link'       => 'Структура',
            'icon'       => 'folder',
            'permission' => [
                'pages_read',
                'nodes_read',
                'faqs_read',
                'tags_read',
            ],
            'children'   => [
                [
                    'link'       => 'Страницы',
                    'route'      => 'oleus.pages',
                    'permission' => 'pages_read'
                ],
                //                    [
                //                        'link'       => 'Страницы тегов',
                //                        'route'      => 'oleus.tags',
                //                        'permission' => 'tags_read'
                //                    ],
                [
                    'link'       => 'Материалы',
                    'route'      => 'oleus.nodes',
                    'permission' => 'nodes_read'
                ],
                //                    [
                //                        'link'       => 'Вопрос / Ответ',
                //                        'route'      => 'oleus.faqs',
                //                        'permission' => 'faqs_read'
                //                    ]
            ]
        ],
        [
            'link'       => 'Компоненты',
            'icon'       => 'create_new_folder',
            'permission' => [
                'menus_read',
                'blocks_read',
                'banners_read',
                'variables_read',
                'advantages_read',
                'sliders_read',
                //                    'comments_read',
                'journal_read'
            ],
            'children'   => [
                [
                    'link'       => 'Меню',
                    'route'      => 'oleus.menus',
                    'params'     => [],
                    'permission' => 'menus_read'
                ],
                //                                [
                //                                    'link'       => 'Блоки',
                //                                    'route'      => 'oleus.blocks',
                //                                    'permission' => 'blocks_read'
                //                                ],
                [
                    'link'       => 'Баннеры',
                    'route'      => 'oleus.banners',
                    'permission' => 'banners_read'
                ],
                [
                    'link'       => 'Преимущества',
                    'route'      => 'oleus.advantages',
                    'permission' => 'advantages_read'
                ],
                [
                    'link'       => 'Слайд-шоу',
                    'route'      => 'oleus.sliders',
                    'permission' => 'sliders_read'
                ],
                //                    [
                //                        'link'       => 'Комментарии',
                //                        'route'      => 'oleus.comments',
                //                        'permission' => 'comments_read'
                //                    ],
                [
                    'link'       => 'Переменные',
                    'route'      => 'oleus.variables',
                    'permission' => 'variables_read'
                ],
                //                [
                //                    'link'       => 'Журнал событий',
                //                    'route'      => 'oleus.journal',
                //                    'permission' => 'journal_read'
                //                ],
                [
                    'link'       => 'Modal Баннеры',
                    'route'      => 'oleus.modal_banners',
                    'permission' => 'banners_read'
                ]
            ]
        ],
        //        [
        //            'link'       => 'Дилеры',
        //            'icon'       => 'create_new_folder',
        //            'permission' => [
        //                'pharm_cities_read',
        //                'pharm_pharmacies_read',
        //            ],
        //            'children'   => [
        //                [
        //                    'link'       => 'Локации',
        //                    'route'      => 'oleus.pharm_city',
        //                    'permission' => 'pharm_cities_read'
        //                ],
        //                [
        //                    'link'       => 'Дилеры',
        //                    'route'      => 'oleus.pharm_pharmacies',
        //                    'permission' => 'pharm_pharmacies_read'
        //                ],
        //            ]
        //        ],
        [
            'link'       => 'Каталог',
            'icon'       => 'create_new_folder',
            'permission' => [
                //                'shop_brands_read',
                'shop_categories_read',
                'shop_params_read',
                'shop_products_read',
                //                'shop_products_update',
                'shop_form_data_read',
                'shop_orders_read',
            ],
            'children'   => [
                //                                [
                //                                    'link'       => 'Бренды',
                //                                    'route'      => 'oleus.shop_brands',
                //                                    'params'     => [],
                //                                    'permission' => 'shop_brands_read'
                //                                ],
                [
                    'link'       => 'Параметры',
                    'route'      => 'oleus.shop_params',
                    'permission' => 'shop_params_read'
                ],
                [
                    'link'       => 'Категории',
                    'route'      => 'oleus.shop_categories',
                    'permission' => 'shop_categories_read'
                ],
                [
                    'link'       => 'Страницы фильтра',
                    'route'      => 'oleus.shop_filter_pages',
                    'permission' => 'shop_categories_read'
                ],
                [
                    'link'       => 'Товары',
                    'route'      => 'oleus.shop_products',
                    'permission' => 'shop_products_read'
                ],
                [
                    'link'       => 'Списки товаров',
                    'route'      => 'oleus.shop_product_list',
                    'permission' => 'shop_products_read'
                ],
                [
                    'link'       => 'Подарки',
                    'route'      => 'oleus.shop_gifts',
                    'permission' => 'shop_products_read'
                ],
                [
                    'link'       => 'Данные форм',
                    'route'      => 'oleus.shop_forms_data',
                    'permission' => 'shop_form_data_read'
                ],
                [
                    'link'       => 'Заказы',
                    'route'      => 'oleus.shop_orders',
                    'permission' => 'shop_orders_read'
                ]
            ]
        ],
        [
            'link'       => 'Формы',
            'icon'       => 'assignment',
            'permission' => [
                'forms_read',
                'forms_data_read',
            ],
            'children'   => [
                [
                    'link'       => 'Конструктор форм',
                    'route'      => 'oleus.forms',
                    'params'     => [],
                    'permission' => 'forms_read'
                ],
                [
                    'link'       => 'Данные форм',
                    'route'      => 'oleus.forms_data',
                    'permission' => 'forms_data_read'
                ]
            ]
        ],
        [
            'link'       => 'Настройки',
            'icon'       => 'settings',
            'permission' => [
                'settings_read',
            ],
            'children'   => [
                [
                    'link'       => 'Общие',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'overall'
                    ],
                    'permission' => 'settings_read'
                ],
                [
                    'link'       => 'SEO',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'seo'
                    ],
                    'permission' => 'settings_read'
                ],
                [
                    'link'       => 'Валюта',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'currency'
                    ],
                    'permission' => 'settings_read'
                ],
                [
                    'link'       => 'Магазин',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'shops'
                    ],
                    'permission' => 'settings_read'
                ],
                [
                    'link'       => 'Сервисы',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'services'
                    ],
                    'permission' => 'settings_read'
                ],
                [
                    'link'       => 'Контакты',
                    'route'      => 'oleus.settings',
                    'params'     => [
                        'setting' => 'contacts'
                    ],
                    'permission' => 'settings_read'
                ],
                //                [
                //                    'link'       => 'Редиректы',
                //                    'route'      => 'oleus.redirects',
                //                    'permission' => 'settings_read'
                //                ]
            ]
        ],
    ]
];
