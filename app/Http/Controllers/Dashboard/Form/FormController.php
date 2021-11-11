<?php

    namespace App\Http\Controllers\Dashboard\Form;

    use App\Library\BaseController;
    use App\Models\Form\FormFields;
    use App\Models\Form\Forms;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Validator;

    class FormController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:forms_read'
            ]);
            $this->titles = [
                'index'     => 'Список  форм',
                'create'    => 'Создать форму',
                'edit'      => 'Редактировать форму "<strong>:title</strong>"',
                'translate' => 'Перевод формы на "<strong>:locale</strong>"',
                'delete'    => '',
            ];
            $this->base_route = 'forms';
            $this->permissions = [
                'read'   => 'forms_read',
                'create' => 'forms_create',
                'update' => 'forms_update',
                'delete' => 'forms_delete',
            ];
            $this->entity = new Forms();
        }

        public function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_form->tabs[] = [
                'title'   => 'Основные параметры',
                'content' => [
                    field_render('locale', [
                        'type'  => 'hidden',
                        'value' => config('app.default_locale'),
                    ]),
                    field_render('title', [
                        'label'    => 'Заголовок',
                        'value'    => $entity->title,
                        'required' => TRUE
                    ]),
                    field_render('sub_title', [
                        'label' => 'Под заголовок',
                        'value' => $entity->sub_title
                    ]),
                    field_render('body', [
                        'label'      => 'Описание к форме',
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->body,
                        'attributes' => [
                            'rows' => 8,
                        ]
                    ]),
                    '<hr class="uk-divider-icon">',
                    field_render('hidden_title', [
                        'type'     => 'checkbox',
                        'selected' => $entity->hidden_title,
                        'values'   => [
                            1 => 'Скрыть заголовок при выводе на страницу'
                        ]
                    ]),
                    field_render('status', [
                        'type'     => 'checkbox',
                        'name'     => 'status',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'опубликовано'
                        ]
                    ])
                ]
            ];
            if ($entity->exists) {
                $_form->tabs[] = [
                    'title'   => 'Поля формы',
                    'content' => [
                        view('backend.partials.form.items', [
                            'items'  => $entity->_items()
                                ->get(),
                            'entity' => $entity
                        ])->render()
                    ]
                ];
                $_form->tabs[] = [
                    'title'   => 'Настройка формы',
                    'content' => [
                        '<h3 class="uk-heading-line"><span>Кнопки</span></h3>',
                        '<div uk-grid class="uk-grid-divider uk-grid-small uk-child-width-1-2">',
                        '<div>',
                        field_render('button_send', [
                            'label' => '"Отправить"',
                            'value' => $entity->button_send,
                            'help'  => 'Отправка данных формы.',
                        ]),
                        field_render('settings.send.class', [
                            'label' => 'CLASS элемента',
                            'value' => $entity->settings->send->class ?? NULL,
                        ]),
                        '<h4 class="uk-heading-bullet uk-margin-small">Цели Google Analytics & Facebook</h4>',
                        field_render('settings.send.target.use', [
                            'type'     => 'checkbox',
                            'selected' => $entity->settings->send->target->use ?? 0,
                            'values'   => [
                                1 => 'Использовать'
                            ]
                        ]),
                        field_render('settings.send.target.category', [
                            'label' => 'Категория (GA)',
                            'value' => $entity->settings->send->target->category ?? 'SUBMIT_FORM',
                        ]),
                        field_render('settings.send.target.event', [
                            'label' => 'Событие (GA)',
                            'value' => $entity->settings->send->target->event ?? 'COMPLETION_SEND',
                        ]),
                        field_render('settings.send.target.action', [
                            'label' => 'Действие (GA)',
                            'value' => $entity->settings->send->target->action ?? 'SEND_FORM',
                        ]),
                        field_render('settings.send.target.fbq_event', [
                            'label' => 'Событие (FB)',
                            'value' => $entity->settings->send->target->fbq_event ?? 'SEND_FORM',
                        ]),
                        '</div>',
                        '<div>',
                        field_render('button_open_form', [
                            'label' => '"Открыть форму"',
                            'value' => $entity->button_open_form,
                            'help'  => 'Открыть форму в модальном окне.',
                        ]),
                        field_render('settings.open_form.class', [
                            'label' => 'CLASS элемента',
                            'value' => $entity->settings->open_form->class ?? NULL,
                        ]),
                        '<h4 class="uk-heading-bullet uk-margin-small">Цели Google Analytics & Facebook</h4>',
                        field_render('settings.open_form.target.use', [
                            'type'     => 'checkbox',
                            'selected' => $entity->settings->open_form->target->use ?? 0,
                            'values'   => [
                                1 => 'Использовать'
                            ]
                        ]),
                        field_render('settings.open_form.target.category', [
                            'label' => 'Категория (GA)',
                            'value' => $entity->settings->open_form->target->category ?? 'CLICK_BUTTON',
                        ]),
                        field_render('settings.open_form.target.event', [
                            'label' => 'Событие (GA)',
                            'value' => $entity->settings->open_form->target->event ?? 'OPEN_FORM',
                        ]),
                        field_render('settings.open_form.target.action', [
                            'label' => 'Действие (GA)',
                            'value' => $entity->settings->open_form->target->action ?? 'OPEN_FORM_IN_MODAL',
                        ]),
                        field_render('settings.open_form.target.fbq_event', [
                            'label' => 'Событие (FB)',
                            'value' => $entity->settings->open_form->target->fbq_event ?? 'SEND_FORM',
                        ]),
                        '</div>',
                        '</div>',
                        '<h3 class="uk-heading-line"><span>Завершение отправки</span></h3>',
                        field_render('completion_type', [
                            'type'   => 'radio',
                            'label'  => 'Действие после сохранение результата формы',
                            'value'  => $entity->exists ? $entity->completion_type : 1,
                            'values' => [
                                1 => 'Переход на страницу благодарности',
                                2 => 'Показать модальное окно',
                            ],
                        ]),
                        '<hr class="uk-divider-icon">',
                        '<div uk-grid class="uk-grid-divider uk-grid-small">',
                        '<div class="uk-first-column uk-width-1-3">',
                        field_render('completion_page_id', [
                            'label' => 'ID страницы благодарности',
                            'value' => $entity->completion_page_id,
                            'help'  => 'Если не указано либо страница будет удалена, то будет перенаправление на главную страницу'
                        ]),
                        '</div>',
                        '<div class="uk-width-2-3">',
                        field_render('completion_modal_text', [
                            'label'      => 'Сообщение в модальном окне',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->completion_modal_text,
                            'class'      => 'editor-short',
                            'attributes' => [
                                'rows' => 8,
                            ]
                        ]),
                        '</div></div>',
                        '<h3 class="uk-heading-line"><span>Рассылка писем</span></h3>',
                        field_render('email_to_receive', [
                            'label' => 'Email получателей письма',
                            'value' => $entity->email_to_receive,
                            'help'  => 'Список через запятую email получателей. Если поле оставить пустым, то рассылка провдиться не будет.'
                        ]),
                        field_render('email_subject', [
                            'label' => 'Тема письма',
                            'value' => $entity->email_subject
                        ]),
                        field_render('user_email_field_id', [
                            'label' => 'ID поля в форме с email пользователя',
                            'value' => $entity->user_email_field_id,
                            'help'  => 'Указать ID поля из вкладки "Поля формы" в котором хранится email пользовател для отправки ему копии письма. Если оставить пустым письмо пользователю отправляться не будет.'
                        ]),
                    ]
                ];
            }
            $_form->tabs[] = $this->__form_tab_display_style($entity, 'prefix', 'suffix');
            $_form->tabs[] = $this->__form_tab_display_rules($entity);

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
                    ],
                ],
                [
                    'title'   => 'Настройка формы',
                    'content' => [
                        '<h3 class="uk-heading-line"><span>Кнопки</span></h3>',
                        '<div uk-grid class="uk-grid-divider uk-grid-small uk-child-width-1-2">',
                        '<div>',
                        field_render('button_send', [
                            'label' => '"Отправить"',
                            'value' => $entity->getTranslation('button_send', $locale),
                        ]),
                        '</div>',
                        '<div>',
                        field_render('button_open_form', [
                            'label' => '"Открыть форму"',
                            'value' => $entity->getTranslation('button_open_form', $locale),
                        ]),
                        '</div>',
                        '</div>',
                        '<h3 class="uk-heading-line"><span>Завершение отправки</span></h3>',
                        field_render('completion_modal_text', [
                            'label'      => 'Сообщение в модальном окне',
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->getTranslation('completion_modal_text', $locale),
                            'class'      => 'editor-short',
                            'attributes' => [
                                'rows' => 8,
                            ]
                        ]),
                    ]
                ]
            ];

            return $_form;
        }

        protected function _items($_wrap)
        {
            $this->__filter();
            if ($this->filter_clear) {
                return redirect()
                    ->route("oleus.{$this->base_route}");
            }
            $_filter = $this->filter;
            $_user = Auth::user();
            $_items = collect([]);
            $_query = Forms::orderBy('title')
                ->select([
                    '*'
                ])
                ->paginate($this->entity->getPerPage(), ['id']);
            $_buttons = [];
            if ($_user->hasPermissionTo($this->permissions['create'])) {
                $_buttons[] = _l('Создать', "oleus.{$this->base_route}.create", [
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
                    'data' => 'Форма',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: laptop_windows">',
                ]
            ];
            if ($_user->hasPermissionTo($this->permissions['update'])) {
                $_headers[] = [
                    'class' => 'uk-width-xsmall'
                ];
            }
            if ($_query->isNotEmpty()) {
                $_items = $_query->map(function ($_item) use ($_user) {
                    $_table_row = [
                        (string)$_item->id,
                        $_item->title,
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_table_row[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->id
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-button-small',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_table_row;
                });
            }
            $_filters = [];
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
            $this->validate($request, [
                'title' => 'required',
            ], [], [
                'title' => 'Заголовок'
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
                'prefix',
                'suffix',
            ]);
            $_save['hidden_title'] = (int)($_save['hidden_title'] ?? 0);
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_save['background_fid'] = $_background_fid['id'] ?? NULL;
            $_item = Forms::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid'
            ]);

            return $this->__response_after_store($request, $_item);
        }

        public function update(Request $request, Forms $_item)
        {
            $request->offsetUnset('key');
            $_locale = $request->get('locale', config('app.default_locale'));
            $_translate = $request->get('translate', 0);
            if ($_translate) {
                $this->validate($request, [
                    'title' => 'required',
                ], [], [
                    'title' => 'Заголовок',
                ]);
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                    'button_send',
                    'button_open_form',
                    'completion_modal_text',
                ]);
                foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
                $_item->save();
            } else {
                $this->validate($request, [
                    'title'               => 'required',
                    'completion_page_id'  => 'sometimes|nullable|exists_data:pages,id,' . $request->get('completion_page_id'),
                    'user_email_field_id' => 'sometimes|nullable|exists_data:form_fields,id,' . $request->get('user_email_field_id')
                ], [], [
                    'title' => 'Заголовок',
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
                    'prefix',
                    'suffix',
                    'button_send',
                    'button_open_form',
                    'completion_type',
                    'completion_page_id',
                    'completion_modal_text',
                    'settings',
                    'email_subject',
                    'email_to_receive',
                    'send_to_user',
                    'user_email_field_id',
                ]);
                $_save['hidden_title'] = (int)($_save['hidden_title'] ?? 0);
                $_save['status'] = (int)($_save['status'] ?? 0);
                $_save['settings']['send']['target']['use'] = (int)($_save['settings']['send']['target']['use'] ?? 0);
                $_save['settings']['open_form']['target']['use'] = (int)($_save['settings']['open_form']['target']['use'] ?? 0);
                $_save['settings'] = json_encode($_save['settings']);
                try {
                    $_item->update($_save);
                } catch (\Exception $exception) {
                    dd($exception->getMessage());
                }
            }

            return $this->__response_after_update($request, $_item);
        }

        public function field(Request $request, Forms $entity, $action, $key = NULL)
        {
            $commands = [];
            switch ($action) {
                case 'add':
                case 'edit':
                    if (is_numeric($key)) {
                        $_item = FormFields::find($key);
                    } elseif (is_string($key)) {
                        $_item = new FormFields();
                        $_item->type = $key;
                    }
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#forms-field-menu',
                            'data'   => 'uk-open'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'eval',
                        'options' => [
                            'data' => 'UIkit.dropdown("#forms-field-menu").hide();'
                        ]
                    ];
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => view('backend.partials.form.item_modal', compact('_item', 'entity'))
                                ->render(),
                            'classDialog' => 'uk-width-1-2'
                        ]
                    ];
                    break;
                case 'save':
                    $_default_locale = config('app.default_locale');
                    $_save = $request->input('item');
                    $validate_rules = [
                        "item.title.{$_default_locale}" => 'required'
                    ];
                    $validator = Validator::make($request->all(), $validate_rules, [], [
                        "item.title.{$_default_locale}" => 'Заголовок поля',
                    ]);
                    $commands['commands'][] = [
                        'command' => 'removeClass',
                        'options' => [
                            'target' => '#modal-forms-field-form input',
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
                        $_save['form_id'] = $entity->id;
                        $_save['data'] = isset($_save['data']) ? serialize($_save['data']) : NULL;
                        $_save['required'] = (int)($_save['required'] ?? 0);
                        $_save['multiple'] = (int)($_save['multiple'] ?? 0);
                        $_save['status'] = (int)($_save['status'] ?? 0);
                        $_save['hidden_label'] = (int)($_save['hidden_label'] ?? 0);
                        $_save['placeholder_label'] = (int)($_save['placeholder_label'] ?? 0);
                        $_item_id = $_save['id'];
                        unset($_save['id']);
                        FormFields::updateOrCreate([
                            'id' => $_item_id
                        ], $_save);
                        $items = $entity->_items()->orderBy('sort')->get();
                        $_items_output = view('backend.partials.form.items_table', compact('items'))
                            ->render();
                        $commands['commands'][] = [
                            'command' => 'html',
                            'options' => [
                                'target' => '#list-form-items',
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
                    FormFields::find($key)
                        ->delete();
                    $items = $entity->_items()->orderBy('sort')->get();
                    if ($items->isNotEmpty()) {
                        $_items_output = view('backend.partials.form.items_table', compact('items'))
                            ->render();
                    } else {
                        $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                    }
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-form-items',
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

    }
