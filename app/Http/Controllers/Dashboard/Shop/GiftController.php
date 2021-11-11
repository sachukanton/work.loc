<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Models\Seo\UrlAlias;
use App\Models\Shop\Brand;
use App\Models\Shop\Gift;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GiftController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Список Подарков',
            'create'    => 'Добавить подарок',
            'edit'      => 'Редактировать подарок "<strong>:title</strong>"',
            'translate' => '',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:shop_products_read'
        ]);
        $this->base_route = 'shop_gifts';
        $this->permissions = [
            'read'   => 'shop_products_read',
            'create' => 'shop_products_create',
            'update' => 'shop_products_update',
        ];
        $this->entity = new Gift();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_field_name = NULL;
        $_form->tabs = [
            [
                'title'   => 'Основные параметры',
                'content' => [
                    field_render('locale', [
                        'type'  => 'hidden',
                        'value' => $this->defaultLocale,
                    ]),
                    '<div class="uk-grid"><div class="uk-width-1-2">',
                    field_render('preview_fid', [
                        'type'     => 'file',
                        'label'    => 'Изображение подарка',
                        'allow'    => 'jpg|jpeg|gif|png',
                        'values'   => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                        'help'     => 'Рекомендуемый размер изображения 150px/130px',
                        'required' => TRUE,
                    ]),
                    '</div><div class="uk-width-1-2">',
                    field_render('title', [
                        'label'      => 'Подпись под подарком',
                        'value'      => $entity->getTranslation('title', $this->defaultLocale),
                        'required'   => TRUE,
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                    field_render('amount', [
                        'label'      => 'Необходимая сумма',
                        'type'       => 'number',
                        'attributes' => [
                            'step' => 1,
                            'min'  => 0
                        ],
                        'required'   => TRUE,
                        'value'      => $entity->amount
                    ]),
                    '</div></div>',
                    '<hr class="uk-divider-icon">',
                    '<div class="uk-grid"><div class="uk-width-1-2">',
                    field_render('type', [
                        'type'     => 'radio',
                        'selected' => $entity->exists ? $entity->type : 0,
                        'values'   => [
                            'delivery_free' => 'Бесплатная доставка',
                            'scratch_card'  => 'Cкретч карта',
                            'product'       => 'Товар за 0',
                        ]

                    ]),
                    '</div><div class="uk-width-1-2">',
                    field_render('product', [
                        'type'       => 'autocomplete',
                        'label'      => 'Товар',
                        'value'      => $entity->exists && $entity->_product->exists ? $entity->_product->id : NULL,
                        'selected'   => $entity->exists && $entity->_product->exists ? $entity->_product->title : NULL,
                        'class'      => 'uk-autocomplete',
                        'attributes' => [
                            'data-url'   => _r('oleus.shop_gifts.product'),
                            'data-value' => 'name'
                        ],
                        'help'       => 'Начните вводить название товара, который будет представлен как подарок.'
                    ]),
                    '</div></div>',
                    '<hr class="uk-divider-icon">',
                    field_render('sort', [
                        'type'  => 'number',
                        'label' => 'Порядок сортировки',
                        'value' => $entity->exists ? $entity->sort : 0,

                    ]),
                    field_render('status', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'Опубликовано'
                        ]
                    ])
                ],
            ],
        ];

        return $_form;
    }

    protected function _items($_wrap)
    {
        $this->__filter();
        $_filter = $this->filter;
        if ($this->filter_clear) {
            return redirect()
                ->route("oleus.{$this->base_route}");
        }
        $_filters = [];
        $_items = collect([]);
        $_user = Auth::user();
        $_query = Gift::orderByDesc('status')
            ->orderBy('amount')
            ->orderBy('sort')
            ->distinct()
            ->with([
                '_product'
            ])
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
                'data' => 'Подпись под подарком',
            ],
            [
                'style' => 'width: 120px;',
                'class' => 'uk-text-small uk-text-center',
                'data'  => 'Необходимая сумма',
            ],
            [
                'class' => 'uk-width-xsmall uk-text-center',
                'data'  => '<span uk-icon="icon: sort_by_alpha"></span>',
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
                    $_item->title,
                    "{$_item->amount} грн",
                    (string)$_item->sort,
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
        if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
        }
        $_save = $request->only([
            'title',
            'amount',
            'type',
            'status',
            'sort',
        ]);
        $this->validate($request, [
            'title'         => 'required',
            'preview_fid'   => 'required',
            'amount'        => 'required|min:0',
            'product.value' => 'required_if:type,product'
        ], [], [
            'title'         => 'Подпись под подарком',
            'preview_fid'   => 'Изображение подарка',
            'name'          => 'Необходимая сумма',
            'product.value' => 'Товар'
        ]);
        $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
        $_save['status'] = (int)($_save['status'] ?? 0);
        if ($_save['type'] == 'product') {
            $_save['product_id'] = $request->input('product.value');
        }
        $_item = Gift::updateOrCreate([
            'id' => NULL
        ], $_save);
        Session::forget([
            'preview_fid',
        ]);

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, Gift $_item)
    {
        if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
        }
        $this->validate($request, [
            'title'       => 'required',
            'preview_fid' => 'required',
            'amount'      => 'required|min:0'
        ], [], [
            'title'       => 'Подпись под подарком',
            'preview_fid' => 'Изображение подарка',
            'name'        => 'Необходимая сумма'
        ]);
        $_locale = $request->get('locale', DEFAULT_LOCALE);
        $_translate = $request->get('translate', 0);
        if ($_translate) {
            $_save = $request->only([
                'title',
            ]);
            foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
            $_item->save();
        } else {
            $_save = $request->only([
                'title',
                'amount',
                'type',
                'status',
                'sort',
            ]);
            $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            if ($_save['type'] == 'product') {
                $_save['product_id'] = $request->input('product.value');
            }
            app()->setLocale($_locale);
            $_item->update($_save);
        }
        Session::forget([
            'preview_fid',
        ]);

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Brand $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function product(Request $request)
    {
        $_items = [];
        if ($_search = $request->input('search')) {
            $_items = UrlAlias::where('model_default_title', 'like', "%{$_search}%")
                ->where('model_type', Product::class)
                ->limit(8)
                ->get();
            if ($_items->isNotEmpty()) {
                $_items->transform(function ($i) {
                    return [
                        'name' => $i->model_default_title,
                        'view' => NULL,
                        'data' => $i->model_id
                    ];
                })
                    ->toArray();
            }
        }

        return response($_items, 200);
    }
}
