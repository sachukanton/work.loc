<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Block;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class BlockController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:blocks_read'
            ]);
            $this->titles = [
                'index'     => 'Список блоков',
                'create'    => 'Добавить блок',
                'edit'      => 'Редактировать блок "<strong>:title</strong>"',
                'translate' => 'Перевод блока на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->base_route = 'blocks';
            $this->permissions = [
                'read'   => 'blocks_read',
                'create' => 'blocks_create',
                'update' => 'blocks_update',
                'delete' => 'blocks_delete',
            ];
            $this->entity = new Block();
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
                            'label'      => 'Заголовок',
                            'value'      => $entity->getTranslation('title', $this->defaultLocale),
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('sub_title', [
                            'label' => 'Под заголовок',
                            'value' => $entity->getTranslation('sub_title', $this->defaultLocale)
                        ]),
                        field_render('body', [
                            'label'      => 'Содержимое',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->getTranslation('body', $this->defaultLocale),
                            'attributes' => [
                                'rows' => 8,
                            ],
                            'required'   => FALSE
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('hidden_title', [
                            'type'     => 'checkbox',
                            'selected' => $entity->hidden_title,
                            'values'   => [
                                1 => 'Скрыть заголовок при выводе на страницу',
                            ]
                        ]),
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
                $this->__form_tab_media_files($entity),
                $this->__form_tab_display_rules($entity)
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
                        'label'    => 'Заголовок',
                        'value'    => $entity->getTranslation('title', $locale),
                        'required' => TRUE
                    ]),
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->getTranslation('sub_title', $locale)
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

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Block::orderBy('id')
                ->select([
                    '*'
                ])
                ->paginate($this->entity->getPerPage(), ['id']);
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
                        $_item->getTranslation('title', $this->defaultLocale),
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
                'title' => 'required',
            ], [], [
                'title' => 'Заголовок',
                'body'  => 'Содержимое',
            ]);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'hidden_title',
                'status',
                'style_id',
                'style_class',
                'background_fid',
            ]);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['hidden_title'] = (int)($_save['hidden_title'] ?? 0);
            $_item = Block::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Block $_item)
        {
            if ($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->validate($request, [
                'title' => 'required',
                'body'  => 'required',
            ], [], [
                'title' => 'Заголовок',
                'body'  => 'Содержимое',
            ]);
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                    'hidden_title',
                    'status',
                    'style_id',
                    'style_class',
                    'background_fid',
                ]);
                $_save['background_fid'] = $_background_fid['id'] ?? NULL;
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_save['hidden_title'] = (int)($_save['hidden_title'] ?? 0);
                $_item->update($_save);
            }
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_update($request, $_item);
        }

    }
