<?php

    namespace App\Http\Controllers\Dashboard\Form;

    use App\Library\BaseController;
    use App\Library\Dashboard;
    use App\Models\Form\Forms;
    use App\Models\Form\FormsData;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class FormDataController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:forms_data_read'
            ]);
            $this->titles = [
                'index'     => 'Список отправленных форм',
                'create'    => '',
                'edit'      => 'Просмотреть данные формы "<strong>:id</strong>"',
                'translate' => '',
                'delete'    => '',
            ];
            $this->base_route = 'forms_data';
            $this->permissions = [
                'read'   => 'forms_data_read',
                'update' => 'forms_data_update',
                'delete' => 'forms_data_delete',
            ];
            $this->entity = new FormsData();
        }

        public function _form($entity)
        {
            $_form = $this->__form();
            $_form->route_tag = $this->base_route;
            $_form->permission = array_merge($_form->permission, $this->permissions);
            $_form->contents[] = field_render('created_at', [
                'type'  => 'markup',
                'label' => 'Отправлено:',
                'html'  => $entity->created_at->format('d.m.Y H:i'),
            ]);
            $_form->contents[] = field_render('created_at', [
                'type'  => 'markup',
                'label' => 'Уведомлено по почте:',
                'html'  => $entity->notified ? '<span class="uk-text-success">Выполнено</span>' : '<span class="uk-text-danger">Не выполнено</span>',
            ]);
            $_form->contents[] = field_render('referer_path', [
                'type'  => 'markup',
                'label' => 'URL страницы отправки:',
                'html'  => $entity->referer_path ? _l($entity->referer_path, $entity->referer_path, ['attributes' => ['target' => '_blank']]) : '-//-',
            ]);
            $_form->contents[] = '<hr class="uk-divider-icon">';
            foreach ($entity->data as $_field_id => $_data) {
                $_form->contents[] = field_render("field_{$_field_id}", [
                    'type'  => 'markup',
                    'label' => "{$_data->label}:",
                    'html'  => $_data->data ? : '-//-',
                ]);
            }
            $_form->contents[] = field_render('comment', [
                'label'      => 'Комментарий к форме',
                'type'       => 'textarea',
                'value'      => $entity->comment,
                'attributes' => [
                    'rows' => 5,
                ]
            ]);

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
            $_forms_data = FormsData::with([
                '_form'
            ])
                ->when($_filter, function ($query) use ($_filter) {
                    if ($_filter['form'] != 'all') $query->where('form_id', '=', $_filter['form']);
                    if ($_filter['create_from']) $query->where('created_at', '>=', Carbon::parse($_filter['create_from'])->format('Y-m-d 00:00:00'));
                    if ($_filter['create_to']) $query->where('created_at', '<=', Carbon::parse($_filter['create_to'])->format('Y-m-d 23:59:59'));
                })
                ->orderByDesc('created_at')
                ->select([
                    '*'
                ])
                ->paginate($this->entity->getPerPage(), ['id']);
            $_buttons = [];
            $_headers = [
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => 'ID',
                ],
                [
                    'data' => 'Форма',
                ],
                [
                    'style' => 'width: 120px;',
                    'class' => 'uk-text-center',
                    'data'  => '<span uk-icon="icon: timer">',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: mail_outline">',
                ],
                [
                    'class' => 'uk-width-xsmall uk-text-center',
                    'data'  => '<span uk-icon="icon: remove_red_eyevisibility">',
                ]
            ];
            if ($_user->hasPermissionTo($this->permissions['update'])) {
                $_headers[] = [
                    'class' => 'uk-width-xsmall'
                ];
            }
            if ($_forms_data->isNotEmpty()) {
                $_items = $_forms_data->map(function ($_item) use ($_user) {
                    $_table_row = [
                        (string)$_item->id,
                        _l($_item->_form->title, 'oleus.forms.edit', [
                            'p'          => ['id' => $_item->_form->id],
                            'attributes' => ['target' => '_blank']
                        ]),
                        $_item->created_at->format('d.m.Y H:i'),
                        $_item->notified ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                        $_item->status ? '<span class="uk-text-success" uk-icon="icon: check"></span>' : '<span class="uk-text-danger" uk-icon="icon: clearclose"></span>',
                    ];
                    if ($_user->hasPermissionTo($this->permissions['update'])) {
                        $_table_row[] = _l('', "oleus.{$this->base_route}.edit", [
                            'p'          => [
                                'id' => $_item->id
                            ],
                            'attributes' => [
                                'class'   => 'uk-button-icon uk-button uk-button-success uk-button-small',
                                'uk-icon' => 'icon: createmode_editedit'
                            ]
                        ]);
                    }

                    return $_table_row;
                });
            }
            $_all_forms = Forms::pluck('title', 'id');
            if ($_all_forms->isNotEmpty()) $_all_forms->prepend('Все формы', 'all');
            $_filters = [
                [
                    'data' => field_render('form', [
                        'type'     => 'select',
                        'selected' => $_filter['form'] ?? 'all',
                        'class'    => 'uk-select2',
                        'values'   => $_all_forms
                    ])
                ],
                [
                    'class' => 'uk-width-small',
                    'data'  => field_render('create_from', [
                        'value'      => $_filter['create_from'] ?? NULL,
                        'class'      => 'uk-datepicker',
                        'attributes' => [
                            'placeholder' => 'Дата с'
                        ]
                    ])
                ],
                [
                    'class' => 'uk-width-small',
                    'data'  => field_render('create_to', [
                        'value'      => $_filter['create_to'] ?? NULL,
                        'class'      => 'uk-datepicker',
                        'attributes' => [
                            'placeholder' => 'Дата по'
                        ]
                    ])
                ]
            ];
            $_items = $this->__items([
                'buttons'     => $_buttons,
                'headers'     => $_headers,
                'filters'     => $_filters,
                'use_filters' => $_filter ? TRUE : FALSE,
                'items'       => $_items,
                'pagination'  => $_forms_data->links('backend.partials.pagination')
            ]);

            return view('backend.partials.list_items', compact('_items', '_wrap'));
        }

        public function edit(FormsData $_item)
        {
            $_wrap = $this->render([
                'seo.title' => str_replace(':id', "#{$_item->id}::{$_item->_form->title}", $this->titles['edit'])
            ]);
            $_form = $this->_form($_item);
            if (!$_item->status) $_item->update(['status' => 1]);

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }

        public function update(Request $request, FormsData $_item)
        {
            $_save = $request->only([
                'comment',
            ]);
            $_item->update($_save);

            return $this->__response_after_update($request, $_item);
        }

    }
