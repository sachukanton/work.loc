<?php

    namespace App\Library;

    use App\Events\EntityDelete;
    use App\Events\EntitySave;
    use App\Models\User\Role;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    trait Dashboards
    {

        protected $notifications = [
            'created'    => 'Элемент создан',
            'updated'    => 'Элемент обновлен',
            'deleted'    => 'Элемент удален',
            'translated' => 'Элемент переведен',
        ];
        protected $titles = [
            'index'  => NULL,
            'create' => NULL,
            'edit'   => NULL,
            'delete' => NULL,
        ];
        protected $base_route;
        protected $filter;
        protected $filter_clear = FALSE;
        protected $permissions = [];

        public function render($data = [])
        {
            App::setLocale('ru');
            $_dashboard_class = [
                'uk-dashboard',
                'uk-position-relative',
                'uk-margin-remove',
                'uk-padding-remove',
            ];
            wrap()->set('page.style_class', $_dashboard_class);
            wrap()->set('page.scripts', config('os_dashboard.scripts'));
            wrap()->set('page.styles', config('os_dashboard.styles'));
            if ($data) foreach ($data as $_key => $_value) wrap()->set($_key, $_value);

            return wrap()->render();
        }

        public function __form()
        {
            return (object)[
                'title'              => NULL,
                'route'              => NULL,
                'method'             => 'POST',
                'route_tag'          => NULL,
                'theme'              => 'backend.forms.form',
                'use_multi_language' => (bool)config('os_seo.use.multi_language'),
                'languages'          => config('laravellocalization.supportedLocales'),
                'id'                 => NULL,
                'class'              => 'uk-form-stacked',
                'relation'           => FALSE,
                'rollback'           => FALSE,
                'buttons'            => [],
                'permission'         => [
                    'read'      => NULL,
                    'create'    => NULL,
                    'update'    => NULL,
                    'delete'    => FALSE,
                    'translate' => FALSE,
                ],
                'tabs'               => [
                ],
                'contents'           => [
                ]
            ];
        }

        public function __form_tab_display_style($entity, ...$add)
        {
            $_fields[] = field_render('style_id', [
                'label'  => 'ID элемента на странице',
                'value'  => $entity->style_id,
                'prefix' => '<div class="uk-form-row"><div uk-grid class="uk-child-width-1-2"><div>',
                'suffix' => '</div>'
            ]);
            $_fields[] = field_render('style_class', [
                'label'  => 'CLASS элемента на странице',
                'value'  => $entity->style_class,
                'prefix' => '<div>',
                'suffix' => '</div></div>'
            ]);
            if (is_array($add) && in_array('background', $add)) {
                $_fields[] = field_render('background_fid', [
                    'type'   => 'file',
                    'label'  => 'Фоновое изображение',
                    'allow'  => 'jpg|jpeg|gif|png|svg',
                    'values' => $entity->background_fid ? [$entity->_background] : NULL,
                ]);
            }
            if (is_array($add) && in_array('prefix', $add)) {
                $_fields[] = field_render('prefix', [
                    'type'       => 'textarea',
                    'label'      => 'Prefix HTML',
                    'class'      => 'uk-codeMirror',
                    'value'      => $entity->prefix,
                    'attributes' => [
                        'rows' => 8
                    ]
                ]);
            }
            if (is_array($add) && in_array('suffix', $add)) {
                $_fields[] = field_render('suffix', [
                    'type'       => 'textarea',
                    'label'      => 'Suffix HTML',
                    'class'      => 'uk-codeMirror',
                    'value'      => $entity->suffix,
                    'attributes' => [
                        'rows' => 8
                    ]
                ]);
            }

            return [
                'title'   => 'Стиль оформления',
                'content' => $_fields,
            ];
        }

        public function __form_tab_media_files($entity)
        {
            $_field_video_file = NULL;
            $_field_video_youtube = NULL;
//            if ($entity->hasAttribute('video_preview_fid')) {
//                $_field_video_file = '<div class="uk-grid uk-child-width-1-2"><div>';
//                $_field_video_file .= field_render('video_preview_fid', [
//                    'type'   => 'file',
//                    'label'  => 'Изображение для видео файла',
//                    'allow'  => 'jpg|jpeg|gif|png',
//                    'view'   => 'avatar',
//                    'values' => $entity->exists && $entity->_video_preview ? [$entity->_video_preview] : NULL,
//                ]);
//                $_field_video_file .= '</div><div>';
//                $_field_video_file .= field_render('video_fid', [
//                    'type'   => 'file',
//                    'label'  => 'Видео файл',
//                    'allow'  => 'mp4',
//                    'values' => $entity->exists && $entity->_video ? [$entity->_video] : NULL,
//                ]);
//                $_field_video_file .= '</div></div>';
//            }
//            if ($entity->hasAttribute('video_youtube')) {
//                $_field_video_youtube = field_render('video_youtube', [
//                    'label' => 'ID видео на YouTube',
//                    'value' => $entity->video_youtube,
//                    'help'  => 'Вставлять только id видео, а не полностью ссылку на него'
//                ]);
//            }

            return [
                'title'   => 'Медиа файлы',
                'content' => [
                    $_field_video_file,
                    $_field_video_youtube,
                    field_render('medias', [
                        'type'     => 'file',
                        'label'    => 'Вложенные изображения',
                        'view'     => 'gallery',
                        'multiple' => TRUE,
                        'values'   => $entity->exists && ($_medias = $entity->_files_related()->wherePivot('type', 'medias')->get()) ? $_medias : NULL
                    ]),
//                    field_render('files', [
//                        'type'     => 'file',
//                        'label'    => 'Вложенные файлы',
//                        'multiple' => TRUE,
//                        'allow'    => 'txt|doc|docx|xls|xlsx|pdf',
//                        'values'   => $entity->exists && ($_files = $entity->_files_related()->wherePivot('type', 'files')->get()) ? $_files : NULL,
//                    ])
                ]
            ];
        }

        public function __form_tab_seo($entity)
        {
            $_fields = [];
            if (($entity->exists && $entity->_alias->id) || !$entity->exists) {
                $_fields[] = field_render('url.alias', [
                    'label' => 'URL',
                    'value' => $entity->exists ? $entity->_alias->alias : request()->get('alias'),
                    'help'  => 'Если оставить пустым, то URL будет сгенерирован из заголовка'
                ]);
                if ($entity->exists) {
                    $_fields[] = field_render('url.re_render', [
                        'type'     => 'checkbox',
                        'selected' => $entity->_alias->re_render,
                        'values'   => [
                            1 => 'Сгенерировать заново URL при сохранении'
                        ]
                    ]);
                }
            }
            $_fields[] = field_render('meta_title', [
                'label' => 'Title',
                'value' => $entity->meta_title
            ]);
            $_fields[] = field_render('meta_description', [
                'type'       => 'textarea',
                'label'      => 'Description',
                'value'      => $entity->meta_description,
                'attributes' => [
                    'rows' => 5,
                ]
            ]);
            $_fields[] = field_render('meta_keywords', [
                'type'       => 'textarea',
                'label'      => 'Keywords',
                'value'      => $entity->meta_keywords,
                'attributes' => [
                    'rows' => 5,
                ]
            ]);
            $_fields[] = field_render('meta_robots', [
                'type'   => 'select',
                'label'  => 'Robots',
                'value'  => $entity->meta_robots,
                'values' => [
                    'index, follow'     => 'index, follow',
                    'noindex, follow'   => 'noindex, follow',
                    'index, nofollow'   => 'index, nofollow',
                    'noindex, nofollow' => 'noindex, nofollow'
                ],
                'class'  => 'uk-select2'
            ]);
            if (($entity->exists && $entity->_alias->id) || !$entity->exists) {
                $_fields[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>XML карта сайта</span></h3>';
                $_fields[] = field_render('url.sitemap', [
                    'type'      => 'checkbox',
                    'name'      => 'meta_sitemap',
                    'base_name' => 'seo',
                    'selected'  => $entity->exists ? $entity->_alias->sitemap : 1,
                    'values'    => [
                        1 => 'Опубликовать в карте сайта'
                    ]
                ]);
                $_fields[] = field_render('url.changefreq', [
                    'type'   => 'select',
                    'label'  => 'Частота изменения',
                    'value'  => $entity->exists ? $entity->_alias->changefreq : 'monthly',
                    'values' => [
                        'always'  => 'always',
                        'hourly'  => 'hourly',
                        'daily'   => 'daily',
                        'weekly'  => 'weekly',
                        'monthly' => 'monthly',
                        'yearly'  => 'yearly',
                        'never'   => 'never',
                    ],
                    'class'  => 'uk-select2',
                    'prefix' => '<div class="uk-form-row"><div class="uk-grid uk-child-width-1-2"><div>',
                    'suffix' => '</div>'
                ]);
                $i = 0;
                $_values = [];
                while ($i <= 1) {
                    $_values[(string)$i] = $i;
                    $i = $i + 0.1;
                }
                $_fields[] = field_render('url.priority', [
                    'type'   => 'select',
                    'label'  => 'Приоритет',
                    'value'  => $entity->exists ? $entity->_alias->priority : '0.5',
                    'values' => $_values,
                    'class'  => 'uk-select2',
                    'prefix' => '<div>',
                    'suffix' => '</div></div>'
                ]);
            }

            return [
                'title'   => 'SEO',
                'content' => $_fields
            ];
        }

        public function __form_tab_seo_for_translation($entity)
        {
            return [
                'title'   => 'SEO',
                'content' => [
                    field_render('meta_title', [
                        'label' => 'Title',
                        'value' => $entity->meta_title
                    ]),
                    field_render('meta_description', [
                        'type'       => 'textarea',
                        'label'      => 'Description',
                        'value'      => $entity->meta_description,
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('meta_keywords', [
                        'type'       => 'textarea',
                        'label'      => 'Keywords',
                        'value'      => $entity->meta_keywords,
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ])
                ]
            ];
        }

        public function __form_tab_display_rules($entity, ...$exclude)
        {
            return NULL;
            if ($_display_rules = $entity->_display_rules) $_display_rules = $_display_rules->groupBy('rule');
            if (config('os_seo.use.multi_language')) {
                if (!$exclude || ($exclude && !in_array('languages', $exclude))) {
                    $_languages = config('laravellocalization.supportedLocales');
                    if (count($_languages) > 1) {
                        $_selected = ['all'];
                        if ($_display_rules->has('languages')) {
                            $_selected = $_display_rules->get('languages')->map(function ($_item) {
                                return $_item->value;
                            })->toArray();
                        }
                        $_languages_select = [
                            'all' => 'Все'
                        ];
                        foreach ($_languages as $_code => $_data) $_languages_select[$_code] = $_data['native'];
                        $_tab[] = field_render('display_rules.languages', [
                            'type'     => 'checkbox',
                            'label'    => 'Языки интерфейса',
                            'class'    => 'uk-checkboxes-used-all',
                            'values'   => $_languages_select,
                            'selected' => $_selected
                        ]);
                    }
                }
            }
            if (!$exclude || ($exclude && !in_array('user_roles', $exclude))) {
                $_roles = Role::all();
                $_selected = ['all'];
                if ($_display_rules->has('user_roles')) {
                    $_selected = $_display_rules->get('user_roles')->map(function ($_item) {
                        return $_item->value;
                    })->toArray();
                }
                $_roles_select = [
                    'all'  => 'Все',
                    'anon' => 'Анонимный пользователь'
                ];
                foreach ($_roles as $_role) if ($_role->name != 'super_admin') $_roles_select[$_role->name] = $_role->display_name;
                $_tab[] = field_render('display_rules.user_roles', [
                    'type'     => 'checkbox',
                    'label'    => 'Роли пользователей',
                    'values'   => $_roles_select,
                    'class'    => 'uk-checkboxes-used-all',
                    'selected' => $_selected
                ]);
            }
            if (!$exclude || ($exclude && !in_array('pages', $exclude))) {
                $_values = NULL;
                if ($_display_rules->has('pages')) {
                    $_values = $_display_rules->get('pages')->map(function ($_item) {
                        return $_item->value;
                    })->implode("\r\n");
                }
                $_tab[] = field_render('display_rules.pages', [
                    'type'       => 'textarea',
                    'label'      => 'Страницы',
                    'value'      => $_values,
                    'attributes' => [
                        'rows' => 5
                    ],
                    'help'       => 'Список URL станиц, на которых будет выводиться объект. Правила формирования:<ul><li>&lt;front&gt; - главная страница</li><li>articles/article-1 - доступно только для страницы с указаным URL</li><li>articles/* - доступно для всех страниц URL которых начинающихся с маски</li><li>*articles* - доступно для всех страниц URL которых содержит маску</li></ul>'
                ]);
            }

            return [
                'title'   => 'Правила отображения',
                'content' => $_tab
            ];
        }

        public function __items($options = [])
        {
            $_default_options = [
                'base_route'     => $this->base_route,
                'buttons'        => [],
                'headers'        => [],
                'filters'        => [],
                'use_filters'    => FALSE,
                'items'          => collect([]),
                'filteredFields' => NULL,
                'pagination'     => NULL,
                'apiPath'        => NULL,
                'before'         => NULL,
                'after'          => NULL,
            ];

            return (object)array_merge_recursive_distinct($_default_options, $options);
        }

        public function __filter()
        {
            $this->filter = request()->all();
            if (isset($this->filter['page'])) unset($this->filter['page']);
            if ($this->filter) {
                Session::put("{$this->base_route}_filter", $this->filter);
            } else {
                $this->filter = Session::get("{$this->base_route}_filter");
            }
            if (isset($this->filter['clear'])) {
                Session::forget("{$this->base_route}_filter");
                $this->filter_clear = TRUE;
            }
        }

        public function __response_after_store(Request $request, $item)
        {
            event(new EntitySave($item));
            if ($this->base_route) {
                if ($request->input('save_and_create')) {
                    return redirect()
                        ->route("oleus.{$this->base_route}.create")
                        ->with('notice', [
                            'message' => $this->notifications['created'],
                            'status'  => 'success'
                        ]);
                }

                return redirect()
                    ->route("oleus.{$this->base_route}.edit", ['id' => $item->id])
                    ->with('notice', [
                        'message' => $this->notifications['created'],
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->back();
        }

        public function __response_after_update(Request $request, $item)
        {
            event(new EntitySave($item));
            if ($this->base_route) {
                if ($request->input('translate')) {
                    if ($request->input('save_close')) {
                        return redirect()
                            ->route("oleus.{$this->base_route}.edit", ['id' => $item->id])
                            ->with('notice', [
                                'message' => $this->notifications['updated'],
                                'status'  => 'success'
                            ]);
                    }

                    return redirect()
                        ->route("oleus.{$this->base_route}.translate", [
                            'id'       => $item->id,
                            'language' => $request->get('locale')
                        ])
                        ->with('notice', [
                            'message' => $this->notifications['translated'],
                            'status'  => 'success'
                        ]);
                } else {
                    if ($request->input('save_close')) {
                        return redirect()
                            ->route("oleus.{$this->base_route}")
                            ->with('notice', [
                                'message' => $this->notifications['updated'],
                                'status'  => 'success'
                            ]);
                    }

                    return redirect()
                        ->route("oleus.{$this->base_route}.edit", ['id' => $item->id])
                        ->with('notice', [
                            'message' => $this->notifications['updated'],
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->back();
        }

        public function __response_after_destroy(Request $request, $item)
        {
            event(new EntityDelete($item));
            if ($this->base_route) {
                return redirect()
                    ->route("oleus.{$this->base_route}")
                    ->with('notice', [
                        'message' => $this->notifications['deleted'],
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->back();
        }

        public function __can_permission($action = 'read')
        {
            if (isset($this->permissions[$action]) && $this->permissions[$action]) return Auth::user()->can($this->permissions[$action]);

            return TRUE;
        }

    }
