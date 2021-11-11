<?php

    namespace App\Http\Controllers\Dashboard\Seo;

    use App\Library\BaseController;
    use App\Models\Seo\Redirect;
    use App\Models\Seo\UrlAlias;
    use App\Models\Shop\Brand;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;

    class RedirectController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->titles = [
                'index'  => 'Список редиректов',
                'create' => 'Добавить редирект',
                'edit'   => 'Редактировать редирект "<strong>:redirect</strong>"',
                'delete' => '',
            ];
            $this->middleware([
                'permission:settings_read'
            ]);
            $this->base_route = 'redirects';
            $this->permissions = [
                'read'   => '',
                'create' => 'settings_read',
                'update' => 'settings_read',
                'delete' => ''
            ];
            $this->entity = new Brand();
        }

        protected function _items($_wrap)
        {

            //            $_r = Redirect::all();
            //            $_r->each(function ($i) {
            //                $i->redirect = urldecode($i->redirect);
            //                $i->save();
            //            });

            $this->__filter();
            $_filter = $this->filter;
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_filters = [];
            $_items = collect([]);
            $_user = Auth::user();
            $_query = Redirect::from('redirects as r')
                ->leftJoin('url_alias as a', 'a.id', '=', 'r.alias_id')
                ->when($_filter, function ($query) use ($_filter) {
                    if ($_filter['redirect']) {
//                        $_redirect = urlencode($_filter['redirect']);
                        $query->where('r.redirect', 'like', "%{$_filter['redirect']}%");
                    }
                    if ($_filter['alias']) {
                        $query->where('a.alias', 'like', "%{$_filter['alias']}%")
                            ->orWhere('r.link', 'like', "%{$_filter['alias']}%");
                    }
                })
                ->distinct()
                ->select([
                    'r.*'
                ])
                ->with([
                    '_alias'
                ])
                ->paginate($this->entity->getPerPage(), ['r.id']);
            $_buttons = [];
            if ($_user->hasPermissionTo($this->permissions['create'])) {
                $_buttons[] = _l('Добавить', "oleus.{$this->base_route}.item", [
                    'p'          => [
                        'redirect' => $this->entity,
                        'action'   => 'add'
                    ],
                    'attributes' => [
                        'class' => 'uk-button uk-button-success uk-text-uppercase use-ajax'
                    ]
                ]);
            }
            $_headers = [
                [
                    'class' => 'uk-width-expand',
                    'data'  => 'Перенаправление с',
                ],
                [
                    'class' => 'uk-text-nowrap',
                    'data'  => 'Перенаправление на',
                ],
                [
                    'class' => 'uk-width-small uk-text-center',
                    'data'  => 'Статус',
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
                        $_item->redirect,
                        $_item->_alias->exists ? _l(str_limit($_item->_alias->model->getTranslation('title', $this->defaultLocale), 50), $_item->_alias->alias, ['attributes' => ['target' => '_blank']]) : $_item->link,
                        $_item->status,
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_response[] = _l('', "oleus.{$this->base_route}.item", [
                            'p'          => [
                                'id'     => $_item->id,
                                'action' => 'edit'
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-button-small use-ajax',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_response;
                });
            }
            $_filters[] = [
                'data' => field_render('redirect', [
                    'value'      => $_filter['redirect'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Перенаправление с'
                    ]
                ])
            ];
            $_filters[] = [
                'data' => field_render('alias', [
                    'value'      => $_filter['alias'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Перенаправление на'
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

        public function item(Request $request, $action, Redirect $redirect = NULL)
        {
            switch ($action) {
                case 'add':
                case 'edit':
                    $_item = $redirect ? : new Redirect();
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.redirect.item_modal', compact('_item'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'easyAutocomplete'
                    ];
                    break;
                case 'save':
                    $validate_rules = [
                        "item.redirect"  => 'required',
                        'item.link.name' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        "item.redirect"  => 'URL перенаправления',
                        'item.link.name' => 'Ссылка на материал'
                    ]);
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#modal-redirect-item-form input',
                            'data'   => 'uk-form-danger'
                        ]
                    ];
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
                            $field = $field == 'item.link.name' ? 'item.link' : $field;
                            $commands['commands'][] = [
                                'command' => 'addClass',
                                'options' => [
                                    'target' => '#' . generate_field_id($field),
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
                        $_save = $request->only('item')['item'];
                        $_paths = explode(PHP_EOL, str_replace("\r", "", $_save['redirect']));
                        $_paths_data = redirect_paths($_paths);
                        if (isset($_paths_data['add'])) {
                            $_alias_id = NULL;
                            $_link = NULL;
                            if ($_save['link']['value']) {
                                $_alias_id = $_save['link']['value'];
                            } else {
                                $_link = $_save['link']['name'];
                            }
                            if (!is_null($_alias_id) || !is_null($_link)) {
                                $_save['alias_id'] = $_alias_id;
                                $_save['link'] = $_link;
                                if ($redirect) {
                                    $_save['redirect'] = $_paths_data['add'][0];
                                    Redirect::updateOrCreate([
                                        'id' => $redirect->id
                                    ], $_save);
                                } else {
                                    foreach ($_paths_data['add'] as $_redirect) {
                                        $_save['redirect'] = $_redirect;
                                        Redirect::updateOrCreate([
                                            'id' => NULL
                                        ], $_save);
                                    }
                                }
                            }
                        }
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
                    $redirect->delete();
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

        public function link(Request $request)
        {
            $_items = [];
            if ($_search = $request->input('search')) {
                $_str = substr(strstr($_search, '::'), 2, strlen($_search));
                if ($_str) $_search = $_str;
                $_url = new UrlAlias();
                $_items = $_url->_items_for_menu($_search);
            }

            return response($_items, 200);
        }

    }
