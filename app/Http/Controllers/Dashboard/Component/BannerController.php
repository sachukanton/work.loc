<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Banner;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class BannerController extends BaseController
    {

        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:banners_read'
            ]);
            $this->base_route = 'banners';
            $this->permissions = [
                'read'   => 'banners_read',
                'create' => 'banners_create',
                'update' => 'banners_update',
                'delete' => 'banners_delete',
            ];
            $this->titles = [
                'index'     => 'Список баннеров',
                'create'    => 'Добавить баннер',
                'edit'      => 'Редактировать баннер "<strong>:title</strong>"',
                'translate' => 'Перевод баннер на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->entity = new Banner();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_form->tabs = [
                [
                    'title'   => 'Основные параметры',
                    'content' => [
                        field_render('locale', [
                            'type'  => 'hidden',
                            'value' => config('app.default_locale'),
                        ]),
                        field_render('title', [
                            'label'    => 'Название',
                            'value'    => $entity->title,
                            'required' => TRUE
                        ]),
                        field_render('background_fid', [
                            'type'   => 'file',
                            'label'  => 'Фоновое изображение',
                            'allow'  => 'jpg|jpeg|png',
                            'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                            'required' => TRUE
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase uk-margin-remove-top"><span>Ссылка для перехода</span></h3>',
                        field_render('link', [
                            'label' => 'Ссылка для перехода по клику',
                            'value' => $entity->getTranslation('link', $this->defaultLocale),
                        ]),
                        field_render('link_attributes', [
                            'type'       => 'textarea',
                            'label'      => 'Дополнительные атрибуты',
                            'value'      => $entity->link_attributes,
                            'attributes' => [
                                'rows' => 2,
                            ]
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase uk-margin-remove-top"><span>Отобразить на странице категории</span></h3>',
                        field_render('status_category', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->status_category : 0,
                            'values'   => [
                                1 => 'Отобразить',
                            ]
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('status', [
                            'type'     => 'checkbox',
                            'selected' => $entity->exists ? $entity->status : 1,
                            'values'   => [
                                1 => 'Опубликовано',
                            ]
                        ])
                    ]
                ],
                $this->__form_tab_display_style($entity),
                $this->__form_tab_display_rules($entity, 'pages')
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
                    field_render('link', [
                        'label'      => 'Ссылка для перехода по клику',
                        'value'      => $entity->getTranslation('link', $locale),
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                ]
            ];

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Banner::orderByDesc('status')
                ->paginate();
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
                    'data' => 'Название',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: laptop_windows">',
                ]
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
                        $_item->title,
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
            $_items = $this->__items([
                'buttons'    => $_buttons,
                'headers'    => $_headers,
                'items'      => $_items,
                'pagination' => $_query->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function store(Request $request)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->validate($request, [
                'title'          => 'required|string',
                'background_fid' => 'required',
            ], [], [
                'title' => 'Заголовок',
                'background_fid'  => 'Фоновое изображение',
            ]);
            $_save = $request->only([
                'title',
                'link',
                'status',
                'status_category',
                'style_id',
                'style_class',
                'background_fid',
                'link_attributes',
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['status_category'] = (int)($_save['status_category'] ?? 0);
            $_item = Banner::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Banner $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->validate($request, [
                'title'          => 'required|string',
                'background_fid' => 'required',
            ], [], [
                'title' => 'Заголовок',
                'background_fid'  => 'Фоновое изображение',
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'title',
                    'link',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $_save = $request->only([
                    'title',
                    'link',
                    'status',
                    'status_category',
                    'background_fid',
                    'style_id',
                    'style_class',
                    'link_attributes',
                ]);
                $_save['background_fid'] = $_background_fid['id'] ?? NULL;
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_save['status_category'] = (int)($_save['status_category'] ?? 0);
                $_item->update($_save);
            }
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_update($request, $_item);
        }

        public function destroy(Request $request, Banner $_item)
        {
            $_item->delete();

            return $this->__response_after_destroy($request, $_item);
        }
    }
