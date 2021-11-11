<?php

    namespace App\Http\Controllers\Dashboard\Component;

    use App\Library\BaseController;
    use App\Models\Components\Block;
    use App\Models\Components\Comment;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Validator;

    class CommentController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:comments_read'
            ]);
            $this->titles = [
                'index'  => 'Список комментариев',
                'create' => 'Добавить комментарий',
                'edit'   => 'Редактировать комментарий "<strong>:id</strong>"',
                'delete' => '',
            ];
            $this->base_route = 'comments';
            $this->permissions = [
                'read'   => 'comments_read',
                'create' => 'comments_create',
                'update' => 'comments_update',
                'delete' => 'comments_delete',
            ];
            $this->entity = new Comment();
        }

        protected function _form($entity)
        {
            $_form = $this->__form();
            $_form->use_multi_language = FALSE;
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            if ($entity->reply) {
                $_form->tabs = [
                    [
                        'title'   => 'Ответ',
                        'content' => [
                            field_render('model_id', [
                                'label' => 'Материал',
                                'type'  => 'markup',
                                'html'  => _l($entity->model->title, $entity->model->generate_url, ['attributes' => ['_target' => 'blank']])
                            ]),
                            field_render('reply', [
                                'label' => $entity->type == 'review' ? 'Отзыв к которому относится' : 'Комментарий к которому относится',
                                'type'  => 'markup',
                                'html'  => '<div class=\'uk-padding-small-left uk-border-double-add uk-border-color-green\' style=\'border-width: 0 0 0 2px;\'><p>' .
                                    _l("comment #{$entity->reply}", 'oleus.comments.edit', [
                                        'p'          => ['id' => $entity->reply],
                                        'attributes' => [
                                            '_target' => 'blank',
                                            'class'   => 'uk-text-uppercase'
                                        ]
                                    ]) . "</p><p>{$entity->_comment->comment}</p></div>"
                            ]),
                            field_render('email', [
                                'label' => 'Email',
                                'type'  => 'markup',
                                'html'  => $entity->email ? : '- не указано -'
                            ]),
                            field_render('user', [
                                'label' => 'Пользователь',
                                'type'  => 'markup',
                                'html'  => $entity->user_id ? _l((($entity->name . ($entity->name != $entity->_user->full_name ? " ({$entity->_user->full_name})" : NULL)) ? : $entity->_user->full_name), 'oleus.users.edit', [
                                    'p'          => ['id' => $entity->_user->id],
                                    'attributes' => ['_target' => 'blank']
                                ]) : $entity->name
                            ]),
                            field_render('comment', [
                                'label'      => 'Текст ответа',
                                'type'       => 'textarea',
                                'value'      => $entity->comment,
                                'attributes' => [
                                    'rows' => 5,
                                ],
                                'required'   => TRUE
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
                    ]
                ];
            } else {
                $_form->tabs = [
                    [
                        'title'   => $entity->type == 'review' ? 'Отзыв' : 'Комментарий',
                        'content' => [
                            field_render('model_id', [
                                'label' => 'Материал',
                                'type'  => 'markup',
                                'html'  => _l($entity->model->title, $entity->model->generate_url, ['attributes' => ['_target' => 'blank']])
                            ]),
                            field_render('email', [
                                'label' => 'Email',
                                'type'  => 'markup',
                                'html'  => $entity->email ? : '- не указано -'
                            ]),
                            field_render('user', [
                                'label' => 'Пользователь',
                                'type'  => 'markup',
                                'html'  => $entity->user_id ? _l((($entity->name . ($entity->name != $entity->_user->full_name ? " ({$entity->_user->full_name})" : NULL)) ? : $entity->_user->full_name), 'oleus.users.edit', [
                                    'p'          => ['id' => $entity->_user->id],
                                    'attributes' => ['_target' => 'blank']
                                ]) : $entity->name
                            ]),
                            $entity->type == 'review' ? field_render('rate', [
                                'label' => 'Выставленная оценка',
                                'type'  => 'markup',
                                'html'  => $entity::RATE_STAR_LABEL[$entity->rate]
                            ]) : NULL,
                            $entity->type == 'review' ? field_render('advantages', [
                                'label'      => 'Достоинства',
                                'type'       => 'textarea',
                                'attributes' => [
                                    'rows' => 3,
                                ],
                                'value'      => $entity->advantages
                            ]) : NULL,
                            $entity->type == 'review' ? field_render('disadvantages', [
                                'label'      => 'Недостатки',
                                'type'       => 'textarea',
                                'attributes' => [
                                    'rows' => 3,
                                ],
                                'value'      => $entity->disadvantages
                            ]) : NULL,
                            field_render('comment', [
                                'label'      => $entity->type == 'review' ? 'Текст отзыва' : 'Текст комментария',
                                'type'       => 'textarea',
                                'value'      => $entity->comment,
                                'attributes' => [
                                    'rows' => 5,
                                ],
                                'required'   => TRUE
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
                    [
                        'title'   => 'Ответы',
                        'content' => [
                            view('backend.partials.comment.items', [
                                'items'  => $entity->_reply,
                                'entity' => $entity
                            ])->render()
                        ]
                    ]
                ];
            }

            return $_form;
        }

        protected function _items($_wrap)
        {
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Comment::orderBy('status')
                ->orderByDesc('created_at')
                ->select([
                    '*'
                ])
                ->paginate(50, ['id']);
            $_buttons = [];
            //            if ($_user->hasPermissionTo($this->permissions['create'])) {
            //                $_buttons[] = _l('Добавить', "oleus.{$this->base_route}.create", [
            //                    'attributes' => [
            //                        'class' => 'uk-button uk-button-success uk-text-uppercase'
            //                    ]
            //                ]);
            //            }
            $_headers = [
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => 'ID',
                ],
                [
                    'data' => 'Текст комментария',
                ],
                [
                    'class' => 'uk-width-medium',
                    'data'  => 'Материал',
                ],
                [
                    'class' => 'uk-width-small',
                    'data'  => 'Ответ на',
                ],
                [
                    'class' => 'uk-text-center',
                    'style' => 'width: 120px',
                    'data'  => '<span uk-icon="icon: date_range">',
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
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_item) use ($_user) {
                    $_response = [
                        "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                        str_limit($_item->comment, 150),
                        _l(str_limit($_item->model->getTranslation('title', $this->defaultLocale), 50), $_item->model->generate_url, [
                            'attributes' => ['target' => '_blank']
                        ]),
                        $_item->reply ? ($_user->hasPermissionTo($this->permissions['update']) ? _l("comment #{$_item->reply}", 'oleus.comments.edit', ['p' => ['id' => $_item->reply]]) : "comment #{$_item->reply}") : ' - ',
                        $_item->created_at->format('d.m.Y H:i'),
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
                'body'  => 'required',
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

        public function update(Request $request, Comment $_item)
        {
            $this->validate($request, [
                'comment' => 'required',
            ], [], [
                'comment' => 'Комментарий',
            ]);
            $_save = $request->only([
                'status',
                'comment',
                'advantages',
                'disadvantages',
            ]);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item->update($_save);

            return $this->__response_after_update($request, $_item);
        }

        public function item(Request $request, Comment $entity, $action, $id = NULL)
        {
            $commands = [];
            switch ($action) {
                case 'add':
                case 'edit':
                    $_item = $id ? Comment::find($id) : new Comment();
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.comment.item_modal', compact('_item', 'entity'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    break;
                case 'save':
                    $_save = $request->input('item');
                    $validate_rules = [
                        'item.comment' => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        'item.comment' => 'Текст ответа'
                    ]);
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#modal-comment-item-form *',
                            'data'   => 'uk-form-danger'
                        ]
                    ];
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
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
                        $_user = $request->user();
                        $_save['model_id'] = $entity->model_id;
                        $_save['model_type'] = $entity->model_type;
                        $_save['user_id'] = $_user->id;
                        $_save['name'] = $_user->full_name;
                        $_save['email'] = $_user->email;
                        $_save['type'] = $entity->type;
                        $_save['status'] = (int)($_save['status'] ?? 0);
                        $_save['reply'] = $entity->id;
                        $_save['comment'] = strip_tags($_save['comment']);
                        $_item_id = $_save['id'];
                        unset($_save['id']);
                        Comment::updateOrCreate([
                            'id' => $_item_id
                        ], $_save);
                        $items = $entity->_reply;
                        $_items_output = view('backend.partials.comment.items_list', compact('items'))
                            ->render();
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-comment-items',
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
                    Comment::find($id)
                        ->delete();
                    $items = $entity->_reply;
                    if ($items->isNotEmpty()) {
                        $_items_output = view('backend.partials.comment.items_list', compact('items'))
                            ->render();
                    } else {
                        $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                    }
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-comment-items',
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
