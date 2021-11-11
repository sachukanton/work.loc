<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Library\Dashboard;
use App\Models\Shop\Form;
use App\Models\Shop\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormDataController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware([
            'permission:shop_form_data_read'
        ]);
        $this->titles = [
            'index'     => 'Список отправленных форм',
            'create'    => '',
            'edit'      => 'Просмотреть данные формы "<strong>:id</strong>"',
            'translate' => '',
            'delete'    => '',
        ];
        $this->base_route = 'shop_forms_data';
        $this->permissions = [
            'read'   => 'shop_form_data_read',
            'update' => 'shop_form_data_update',
            'delete' => 'shop_form_data_delete',
        ];
        $this->entity = new Form();
    }

    public function _form($entity)
    {
        $_product = Product::find($entity->product_id);
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->class = 'uk-form-horizontal';
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_form->contents[] = field_render('created_at', [
            'type'  => 'markup',
            'label' => 'Отправлено:',
            'html'  => $entity->created_at->format('d.m.Y H:i'),
        ]);
        $_form->contents[] = field_render('form', [
            'type'  => 'markup',
            'label' => 'Форма:',
            'html'  => $entity->type,
        ]);
        $_form->contents[] = field_render('name', [
            'type'  => 'markup',
            'label' => 'Имя:',
            'html'  => $entity->name ? : '-//-',
        ]);
        $_form->contents[] = field_render('phone', [
            'type'  => 'markup',
            'label' => 'Номер телефона:',
            'html'  => $entity->phone ? format_phone_number($entity->phone)['format_render'] : '-//-',
        ]);
        $_form->contents[] = field_render('email', [
            'type'  => 'markup',
            'label' => 'E-mail адрес:',
            'html'  => $entity->email ? : '-//-',
        ]);
        $_form->contents[] = field_render('product', [
            'type'  => 'markup',
            'label' => 'Выбранный товар:',
            'html'  => $_product ? _l($_product->title, $_product->generate_url, ['attributes' => ['target' => '_blank']]) : $entity->product_name,
        ]);
        $_form->contents[] = field_render('quantity', [
            'type'  => 'markup',
            'label' => 'Количество товара, ед.:',
            'html'  => $entity->quantity ? : 1,
        ]);
        $_form->contents[] = field_render('price', [
            'type'  => 'markup',
            'label' => 'Цена товара за ед.:',
            'html'  => $entity->price ? view_price($entity->price, $entity->price)['format']['view_price_2'] : '-//-',
        ]);
        $_form->contents[] = field_render('comment', [
            'type'  => 'markup',
            'label' => 'Комментарий пользователя:',
            'html'  => $entity->comment ? : '-//-',
        ]);
        foreach ($entity->data as $_field_id => $_data) {
            $_form->contents[] = field_render("field_{$_field_id}", [
                'type'  => 'markup',
                'label' => "{$_data->label}:",
                'html'  => $_data->data ? : '-//-',
            ]);
        }
        $_form->contents[] = field_render('referer_path', [
            'type'  => 'markup',
            'label' => 'URL страницы отправки:',
            'html'  => $entity->referer_path ? _l($entity->referer_path, $entity->referer_path, ['attributes' => ['target' => '_blank']]) : '-//-',
        ]);
        $_form->contents[] = '<hr class="uk-divider-icon">';
        $_form->contents[] = field_render('manager_comment', [
            'label'      => 'Комментарий менеджера к заявке:',
            'type'       => 'textarea',
            'value'      => $entity->manager_comment,
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
        $_forms_data = Form::with([
            '_product'
        ])
            ->when($_filter, function ($query) use ($_filter) {
                if ($_filter['form'] != 'all') $query->where('form', '=', $_filter['form']);
                if ($_filter['create_from']) $query->where('created_at', '>=', Carbon::parse($_filter['create_from'])->format('Y-m-d 00:00:00'));
                if ($_filter['create_to']) $query->where('created_at', '<=', Carbon::parse($_filter['create_to'])->format('Y-m-d 23:59:59'));
            })
            ->orderBy('status')
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
                'class' => 'uk-width-medium',
                'data'  => 'Имя',
            ],
            [
                'class' => 'uk-width-medium',
                'data'  => 'Номер телефона',
            ],
            [
                'class' => 'uk-width-medium',
                'data'  => 'Товар',
            ],
            [
                'style' => 'width: 120px;',
                'class' => 'uk-text-center',
                'data'  => '<span uk-icon="icon: timer">',
            ],
            [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: contact_mail">',
            ]
        ];
        if ($_user->hasPermissionTo($this->permissions['update'])) {
            $_headers[] = [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: createmode_editedit">',
            ];
        }
        if ($_forms_data->isNotEmpty()) {
            $_items = $_forms_data->map(function ($_item) use ($_user) {
                $_phone = $_item->phone ? format_phone_number($_item->phone)['format_render'] : '-//-';
                $_table_row = [
                    (string)$_item->id,
                    $_item->type,
                    $_item->name,
                    $_phone,
                    $_item->_product->id ? _l($_item->_product->title, "oleus.shop_products.edit", [
                        'p'          => ['id' => $_item->_product->id],
                        'attributes' => ['target' => '_blank']
                    ]) : $_item->_product_name,
                    $_item->created_at->format('d.m.Y H:i'),
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
        $_all_forms = collect(Form::FORM_TYPE);
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

    public function edit(Form $_item)
    {
        $_wrap = $this->render([
            'seo.title' => str_replace(':id', "#{$_item->id}::{$_item->type}", $this->titles['edit'])
        ]);
        $_form = $this->_form($_item);

        return view($_form->theme, compact('_form', '_item', '_wrap'));
    }

    public function update(Request $request, Form $_item)
    {
        $_save = $request->only([
            'manager_comment',
        ]);
        $_item->update($_save);

        return $this->__response_after_update($request, $_item);
    }

}
