<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Exports\OrderExport;
use App\Library\BaseController;
use App\Library\Dashboard;
use App\Models\Shop\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware([
            'permission:shop_orders_read'
        ]);
        $this->titles = [
            'index'     => 'Заказы',
            'create'    => '',
            'edit'      => 'Состав заказа "<strong>:id</strong>"',
            'translate' => '',
            'delete'    => '',
        ];
        $this->base_route = 'shop_orders';
        $this->permissions = [
            'read'   => 'shop_orders_read',
            'update' => 'shop_orders_update',
            'delete' => 'shop_orders_delete',
        ];
        $this->entity = new Order();
    }

    public function _form($entity)
    {
        $entity->amount_view = view_price($entity->amount, $entity->amount);
        $entity->order_products = $entity->_products->transform(function ($_item) {
            $_item->price_view = view_price($_item->price, $_item->price);
            $_item->amount_view = view_price($_item->amount, $_item->amount);
            $_item->amount_less_discount_view = view_price($_item->amount_less_discount, $_item->amount_less_discount);
            $_item->sku = $_item->_product ? ($_item->_product->model ? : $_item->_product->sku) : NULL;
            $_item->url = $_item->_product ? $_item->_product->generate_url : NULL;

            return $_item;
        });
        $_order_class = 'uk-background-color-grey';
        switch ($entity->status) {
            case 0:
            case 1:
                $_order_class = 'uk-background-color-blue uk-light';
                break;
            case 2:
                $_order_class = 'uk-background-color-green uk-light';
                break;
            case 3:
                $_order_class = 'uk-background-color-green uk-light';
                break;
            case 4:
                $_order_class = 'uk-background-color-red uk-light';
                break;
        }
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->class = 'uk-form-horizontal';
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_form->contents[] = "<div class='uk-text-right'><div class='{$_order_class} uk-button uk-button-large uk-text-uppercase'>" . trans($entity::ORDER_STATUS[$entity->status]) . "</div></div>";
        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Данные заказа</span></h3>';
        $_form->contents[] = field_render('id', [
            'type'  => 'markup',
            'label' => 'ID заказа:',
            'html'  => "#{$entity->id}",
        ]);
        $_form->contents[] = field_render('created_at', [
            'type'  => 'markup',
            'label' => 'Дата оформления:',
            'html'  => $entity->created_at->format('d.m.Y'),
        ]);
        $_form->contents[] = field_render('created_time', [
            'type'  => 'markup',
            'label' => 'Время заказа:',
            'html'  => $entity->created_at->format('H:i'),
        ]);
        $_form->contents[] = field_render('person', [
            'type'  => 'markup',
            'label' => 'Колчиество персон:',
            'html'  => $entity->person ? : '-//-',
        ]);
        $_birthday = $entity->birthday == 1 ? 'Да' : 'Нет';
        $_form->contents[] = field_render('birthday', [
            'type'  => 'markup',
            'label' => 'У меня День рождения:',
            'html'  => $_birthday ? : '-//-',
        ]);
        $_call_me_back = $entity->call_me_back == 1 ? 'Да' : 'Нет';
        $_form->contents[] = field_render('call_me_back', [
            'type'  => 'markup',
            'label' => 'Перезвонить:',
            'html'  => $_call_me_back ? : '-//-',
        ]);
        $_form->contents[] = field_render('comment', [
            'type'  => 'markup',
            'label' => 'Комментарий пользователя:',
            'html'  => $entity->comment ? : '-//-',
        ]);
        //        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Данные R-Keeper Delivery</span></h3>';
        //        $_form->contents[] = field_render('rk_order_id', [
        //            'type'  => 'markup',
        //            'label' => 'ID заказа:',
        //            'html'  => $entity->rk_order_id ? : '-//-',
        //        ]);
        //        $_form->contents[] = field_render('rk_status', [
        //            'type'  => 'markup',
        //            'label' => 'Статус заказа:',
        //            'html'  => $entity->rk_order_id ? 'SUCCESS' : ($entity->rk_order_data ? 'FAILED' : '-//-'),
        //        ]);
        //        $_form->contents[] = field_render('coupon', [
        //            'type'  => 'markup',
        //            'label' => 'Сертификат:',
        //            'html'  => $entity->coupon ? : '-//-',
        //        ]);
        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Персональная информация</span></h3>';
        $_form->contents[] = field_render('surname', [
            'type'  => 'markup',
            'label' => 'Фамилия:',
            'html'  => $entity->surname ? : '-//-',
        ]);
        $_form->contents[] = field_render('name', [
            'type'  => 'markup',
            'label' => 'Имя:',
            'html'  => $entity->name ? : '-//-',
        ]);
        $_form->contents[] = field_render('phone', [
            'type'  => 'markup',
            'label' => 'Номер телефона:',
            'html'  => $entity->phone ? : '-//-',
        ]);
        $_form->contents[] = field_render('email', [
            'type'  => 'markup',
            'label' => 'E-mail адрес:',
            'html'  => $entity->email ? : '-//-',
        ]);
        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Оплата и доставка</span></h3>';
        $_form->contents[] = field_render('payment_method', [
            'type'  => 'markup',
            'label' => 'Способ оплаты:',
            'html'  => $entity->payment_method ? trans("shop.payment_methods.payment_method_{$entity->payment_method}") : '-//-',
        ]);
        if ($entity->payment_method == 'cash') {
            $_form->contents[] = field_render('surrender', [
                'type'  => 'markup',
                'label' => 'Сдача с, грн.:',
                'html'  => $entity->surrender ? : '-//-',
            ]);
        }
        if ($entity->payment_method == 'card') {
            $_form->contents[] = field_render('payment_status', [
                'type'  => 'markup',
                'label' => 'Статус оплаты:',
                'html'  => $entity->payment_status ? 'Оплачено' : 'Не оплачено',
            ]);
            $_form->contents[] = field_render('payment_transaction_number', [
                'type'  => 'markup',
                'label' => 'Номер транзакции:',
                'html'  => $entity->payment_transaction_number ? : '-//-',
            ]);
            $_form->contents[] = field_render('payment_transaction_status', [
                'type'  => 'markup',
                'label' => 'Статус транзакции:',
                'html'  => $entity->payment_transaction_number ? $entity::LIQPAY_STATUS[$entity->payment_transaction_status] : '-//-',
            ]);
        }
        $_form->contents[] = field_render('delivery_method', [
            'type'  => 'markup',
            'label' => 'Способ доставки:',
            'html'  => $entity->delivery_method ? trans("shop.delivery_method.delivery_method_{$entity->delivery_method}") : '-//-',
        ]);
        if ($entity->delivery_method == 'delivery') {
            $_form->contents[] = field_render('delivery_address', [
                'type'  => 'markup',
                'label' => 'Адрес доставки:',
                'html'  => $entity->delivery_method == 'delivery' ? $entity->formation_address : '-//-',
            ]);
        }
        $_form->contents[] = field_render('delivery_free', [
            'type'  => 'markup',
            'label' => 'Бесплатная доставка:',
            'html'  => $entity->delivery_free || $entity->delivery_method == 'pickup' ? 'Да' : 'Нет',
        ]);
        $_form->contents[] = field_render('pre_order_at', [
            'type'  => 'markup',
            'label' => 'Доставка на время:',
            'html'  => $entity->pre_order_at ? $entity->pre_order_at->format('Y-m-d H:i:s') : 'Нет',
        ]);
        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Подарок к заказу</span></h3>';
        $_form->contents[] = field_render('gifts', [
            'type'  => 'markup',
            'label' => 'Подарок:',
            'html'  => $entity->_gift->exists ? $entity->_gift->title : 'Нет',
        ]);
        $_form->contents[] = '<h3 class="uk-heading-line uk-text-uppercase"><span>Состав заказа</span></h3>';
        $_form->contents[] = view('backend.partials.shop.order.products', [
            '_items'  => $entity->order_products,
            '_entity' => $entity,
        ])->render();
        $_form->contents[] = '<hr class="uk-divider-icon">';
//        $_form->contents[] = field_render('attach_file', [
//            'type'   => 'file',
//            'label'  => 'Прикрепить счет-фактуру',
//            'allow'  => 'pdf',
//            'values' => $entity->exists && $entity->_attach_file ? [$entity->_attach_file] : NULL,
//        ]);
        if ($entity->status < 2) {
            $_form->contents[] = field_render('status', [
                'label'    => 'Статус заказа:',
                'type'     => 'select',
                'selected' => $entity->status,
                'class'    => 'uk-select2',
                'values'   => [
                    0 => 'shop.status.0',
                    1 => 'shop.status.1',
                    2 => 'shop.status.2',
                    3 => 'shop.status.3',
                ]
            ]);
        }
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
        $_orders = Order::with([
            '_products'
        ])
            ->when($_filter, function ($query) use ($_filter) {
                if ($_filter['order_id']) $query->where('id', '=', $_filter['order_id']);
                if ($_filter['status'] != 'all') $query->where('status', '=', $_filter['status']);
                if ($_filter['type'] != 'all') $query->where('type', '=', $_filter['type']);
                if ($_filter['create_from']) $query->where('created_at', '>=', Carbon::parse($_filter['create_from'])->format('Y-m-d 00:00:00'));
                if ($_filter['create_to']) $query->where('created_at', '<=', Carbon::parse($_filter['create_to'])->format('Y-m-d 23:59:59'));
                if ($_filter['phone']) $query->where('phone', 'like', "%{$_filter['phone']}%");
            })
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->select([
                '*'
            ])
            ->paginate($this->entity->getPerPage(), ['id']);
        $_buttons = [];
        $_headers[] = [
            'class' => 'uk-width-xsmall uk-text-center',
            'data'  => 'ID',
        ];
        $_headers[] = [
            'class' => 'uk-width-100',
            'data'  => 'Тип',
        ];
        $_headers[] = [
            'data' => 'Клиент',
        ];
        $_headers[] = [
            'class' => 'uk-width-130',
            'data'  => 'Номер телефона',
        ];
        $_headers[] = [
            'class' => 'uk-width-150',
            'data'  => 'Метод доставки',
        ];
        $_headers[] = [
            'class' => 'uk-width-150',
            'data'  => 'Метод оплаты',
        ];
        $_headers[] = [
            'class' => 'uk-text-right uk-width-130',
            'data'  => 'Сумма, грн',
        ];
        $_headers[] = [
            'class' => 'uk-text-center uk-width-130',
            'data'  => '<span uk-icon="icon: timer"> Предзаказ',
        ];
        $_headers[] = [
            'class' => 'uk-text-center uk-width-130',
            'data'  => '<span uk-icon="icon: timer"> Создания',
        ];
        $_headers[] = [
            'class' => 'uk-text-center uk-width-130',
            'data'  => '<span uk-icon="icon: playlist_add_check"></span>',
        ];
        if ($_user->hasPermissionTo($this->permissions['update'])) {
            $_headers[] = [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: createmode_editedit">',
            ];
        }
        if ($_orders->isNotEmpty()) {
            $_items = $_orders->map(function ($_item) use ($_user) {
                $_amount = $_item->amount_less_discount ? : $_item->amount;
                $_table_row = [
                    (string)$_item->id,
                    $_item->type == 'full' ? 'Полный' : 'Быстрый',
                    $_item->user_full_name,
                    $_item->format_phone,
                    $_item->delivery_method ? trans('shop.delivery_method.delivery_method_' . $_item->delivery_method) : '-//-',
                    $_item->payment_method ? trans('shop.payment_method.payment_method_' . $_item->payment_method) : '-//-',
                    '<strong>' . (string)view_price($_amount, $_amount)['format']['view_price'] . '</strong>',
                    $_item->pre_order_at ? $_item->pre_order_at->format('d.m.Y H:i') : 'Нет',
                    $_item->created_at->format('d.m.Y H:i'),
                    trans($_item::ORDER_STATUS[$_item->status]),
                ];
                if ($_user->hasPermissionTo($this->permissions['update'])) {
                    $_table_row[] = _l('', "oleus.{$this->base_route}.edit", [
                        'p'          => [
                            'id' => $_item->id
                        ],
                        'attributes' => [
                            'class'   => 'uk-button-icon uk-button uk-button-success uk-button-small',
                            'uk-icon' => 'icon: remove_red_eyevisibility'
                        ]
                    ]);
                }

                return [
                    'class' => "order-status-{$_item->status}",
                    'data'  => $_table_row
                ];
            });
        }
        $_filters = [
            [
                'class' => 'uk-width-small uk-margin-small-bottom',
                'data'  => field_render('type', [
                    'type'     => 'select',
                    'selected' => $_filter['type'] ?? 'all',
                    'values'   => [
                        'all'   => '- выбрать тип -',
                        'full'  => 'Полный заказ',
                        'quick' => 'Быстрый заказ',
                    ],
                    'class'    => 'uk-select2',
                ])
            ],
            [
                'class' => 'uk-width-small uk-margin-small-bottom',
                'data'  => field_render('order_id', [
                    'value'      => $_filter['order_id'] ?? NULL,
                    'attributes' => [
                        'placeholder' => 'Номер заказа',
                    ]
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
            ],
            [
                'class' => 'uk-width-medium uk-margin-small-bottom',
                'data'  => field_render('phone', [
                    'value'      => $_filter['phone'] ?? NULL,
                    'class'      => 'uk-phone-mask',
                    'attributes' => [
                        'placeholder' => 'Номер телефона'
                    ]
                ])
            ],
            [
                'class' => 'uk-width-medium uk-margin-small-bottom',
                'data'  => field_render('status', [
                    'type'     => 'select',
                    'selected' => $_filter['status'] ?? 'all',
                    'values'   => [
                        'all' => '- выбрать статус -',
                        -1    => 'Проблемы с оплатой',
                        0     => 'Ожидается оплата',
                        1     => 'Новый',
                        2     => 'Обрабатывается',
                        3     => 'Обработан',
                        4     => 'Отменен',
                    ],
                    'class'    => 'uk-select2',
                ])
            ],
        ];
        $_items = $this->__items([
            'buttons'     => $_buttons,
            'headers'     => $_headers,
            'filters'     => $_filters,
            'use_filters' => $_filter ? TRUE : FALSE,
            'items'       => $_items,
            'pagination'  => $_orders->links('backend.partials.pagination')
        ]);

        return view('backend.partials.list_items', compact('_items', '_wrap'));
    }

    public function edit(Order $_item)
    {
        $_wrap = $this->render([
            'seo.title' => str_replace(':id', "#{$_item->id}", $this->titles['edit'])
        ]);
        $_form = $this->_form($_item);

        return view($_form->theme, compact('_form', '_item', '_wrap'));
    }

    public function update(Request $request, Order $_item)
    {
        if ($attach_file = $request->input('attach_file')) {
            $_attach_file = array_shift($attach_file);
            Session::flash('attach_file', json_encode([f_get($_attach_file['id'])]));
        }
        if (!$request->hasFile('attach_file')) $request->request->remove('attach_file');
        $this->validate($request, [
            'attach_file' => 'sometimes|required|mimes:pdf',
        ], [], [
            'title' => 'Файл счет-фактуры',
        ]);
        $_save = $request->only([
            'manager_comment',
            'status',
        ]);
        $_save['attach_file'] = $_attach_file['id'] ?? NULL;
        $_item->update($_save);
        Session::forget([
            'attach_file',
        ]);

        return $this->__response_after_update($request, $_item);
    }

    public function download(Request $request, Order $order)
    {
        ini_set('memory_limit', '2048M');
        $request->request->add([
            'order_id' => $order->id
        ]);

        return Excel::download(new OrderExport(), "order_{$order->id}.xlsx");
    }

    public function view_order(Request $request)
    {
        $_response = [];
        $_item_id = $request->get('item');
        if ($_item_id) {
            $_item = Order::find($_item_id);
            $_response['commands'][] = [
                'command' => 'UK_modal',
                'options' => [
                    'content'     => View::make('backend.shop.partials.modal_view_order', compact('_item'))
                        ->render(function ($_content) {
                            return clear_html($_content);
                        }),
                    'id'          => "ajax-modal-order-{$_item->id}",
                    'classDialog' => 'uk-width-1-1',
                ]
            ];
        } else {
            $_response['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'status' => 'primary',
                    'text'   => 'Данные по выбранному заказу не найдены.'
                ]
            ];
        }

        return response($_response, 200);
    }

    public function save_order(Request $request)
    {
        $_response = [];
        $_order = Order::find($request->get('item'));
        if ($request->has('recalculate')) {
            $_quantity = $request->get('quantity', []);
            $_remove = $request->get('remove', []);
            $_amount = 0;
            $_order->_products->transform(function ($_item) use (&$_amount, $_quantity, $_remove) {
                $_q = $_quantity[$_item->id];
                if ($_q <= 0) $_q = 0;
                if ($_q != $_item->quantity) {
                    $_item->amount = $_item->price * $_q;
                    $_item->quantity = $_q;
                }
                if (!isset($_remove[$_item->id]) && $_q != 0) {
                    $_item->status = 0;
                    $_amount += $_item->amount;
                } else {
                    $_item->status = 1;
                    $_item->amount = 0;
                    $_item->quantity = 0;
                }
                $_item->save();

                return $_item;
            });
            $_order->amount = $_amount;
            if($_order->discount){
                if($_order->delivery_method = 'pickup'){
                    $_percent = config('os_shop.delivery_pickup_percent', 0);
                    $_order->discount = ceil($_order->amount * ($_percent / 100));
                    $_order->amount_less_discount = $_order->amount - $_order->discount;
                }
            }
            if ($_amount == 0) {
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'status' => 'warning',
                        'text'   => '<strong>Внимание!</strong> В обрабатываемом заказе нет реализуемых товаров.<br>Проверте правильность введенных данных.'
                    ]
                ];
                //                $_order->status = 4;
            } else {
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'status' => 'success',
                        'text'   => 'Состав заказа пересчитан.'
                    ]
                ];
            }
            $_order->save();
            $_amount = view_price($_order->amount, $_order->amount);
            $_response['commands'][] = [
                'command' => 'text',
                'options' => [
                    'target' => '#form-field-order-amount-data',
                    'data'   => "{$_amount['format']['view_price']} {$_amount['currency']['suffix']}"
                ]
            ];
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#form-field-order-products-table',
                    'data'   => View::make('backend.shop.partials.items_view_order', ['_item' => $_order])
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        })
                ]
            ];
            $_order->save();
        } else {
            $_order->status = $request->get('status', 2);
            $_order->manager_comment = $request->get('comment');
            if ($_order->amount == 0) $_order->status = 4;
            $_order->save();
            $_response['commands'][] = [
                'command' => 'UK_modalClose',
                'options' => [
                    'target' => "#ajax-modal-order-{$_order->id}"
                ]
            ];
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#box-list-new-orders',
                    'data'   => View::make('backend.shop.partials.items_new_orders', ['_items' => Order::get_new_orders()])
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        })
                ]
            ];
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#box-list-last-complete-orders',
                    'data'   => View::make('backend.shop.partials.items_last_complete_orders', ['_items' => Order::get_complete_orders()])
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        })
                ]
            ];
        }

        return response($_response, 200);
    }

    public function order_lists_update(Request $request)
    {
        $_response = NULL;
        $_last_updated_at = $request->get('last_updated_at');
        $_order_last_updated_at = Order::max('updated_at');
        $_order_last_updated_at = strtotime($_order_last_updated_at);
        $_update = FALSE;
        if (($_last_updated_at && $_last_updated_at != $_order_last_updated_at) || is_null($_last_updated_at)) {
            $_update = TRUE;
            $_response['commands'][] = [
                'command' => 'eval',
                'options' => [
                    'data' => "window.update_order_lists_last_create_at = {$_order_last_updated_at};",
                ]

            ];
        }
        if ($_update) {
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#box-list-new-orders',
                    'data'   => View::make('backend.shop.partials.items_new_orders', [
                        '_items'    => Order::get_new_orders(),
                        '_authUser' => request()->user()
                    ])
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        })
                ]
            ];
            $_response['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#box-list-last-complete-orders',
                    'data'   => View::make('backend.shop.partials.items_last_complete_orders', [
                        '_items'    => Order::get_complete_orders(),
                        '_authUser' => request()->user()
                    ])
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        })
                ]
            ];
        }

        return response($_response, 200);
    }

}
