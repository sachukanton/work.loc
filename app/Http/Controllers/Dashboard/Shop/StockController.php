<?php
namespace App\Http\Controllers\Dashboard\Shop;

//namespace Iiko\Biz;

use App\Library\BaseController;
use App\Models\Seo\UrlAlias;

//use App\Models\Shop\Brand;
//use App\Models\Shop\Gift;
//use App\Models\Shop\Product;

use App\Models\Shop\Stock;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//use Iiko\Biz\Api\Auth;
//use Iiko\Biz\Client as IikoClient;
//use Iiko\Biz\Api\Organizations;

class StockController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->titles = [
            'index'     => 'Список Промокодов',
            'create'    => 'Добавить Промокод',
            'edit'      => 'Редактировать Промокод "<strong>:title</strong>"',
            'translate' => '',
            'delete'    => '',
        ];

        $this->middleware([
            'permission:shop_products_read'
        ]);

        $this->base_route = 'shop_stock';

        $this->permissions = [
            'read'   => 'shop_products_read',
            'create' => 'shop_products_create',
            'update' => 'shop_products_update',
         ];

        $this->entity = new Stock();
    }


     /**
     * @return Auth
     */
    public function AuthApi(): Auth
    {
        return new Auth($this);
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
                    field_render('title', [
                        'label'      => 'Название',
                        'value'      => $entity->getTranslation('title', $this->defaultLocale),
                        'required'   => TRUE,
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                    '</div><div class="uk-width-1-2">',
                    '</div></div>',
                    '<hr class="uk-divider-icon">',
                    '<div class="uk-grid"><div class="uk-width-1-2">',

                    field_render('code', [
                        'label'      => 'Код скидки (iiko.help)',
                        'value'      => $entity->code,
                        'required'   => TRUE,
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                    field_render('date_to', [
                        'label'      => 'До какого числа скидка?',
                        'value'      => $entity->date_to,
                        'type'     => 'date',
                        'required'   => TRUE,
                        'attributes' => [
                            'autofocus' => TRUE,
                        ],
                    ]),
                    '</div><div class="uk-width-1-2">',
                    field_render('type', [
                        'type'     => 'radio',
                        'selected' => $entity->exists ? $entity->type : 0,
                        'values'   => Stock::type()
                    ]),
                    '</div></div>',
                    '<hr class="uk-divider-icon">',
                    '<div class="uk-grid"><div class="uk-width-1-2">',

                    field_render('status', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'Активна'
                        ]
                    ]),

                  
                   

 /* field_render('preview_fid', [
                        'type'     => 'file',
                        'label'    => 'Изображение подарка',
                        'allow'    => 'jpg|jpeg|gif|png',
                        'values'   => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                        'help'     => 'Рекомендуемый размер изображения 150px/130px',
                        'required' => TRUE,
                    ]),*/
                    /*field_render('amount', [
                        'label'      => 'Необходимая сумма',
                        'type'       => 'number',
                        'attributes' => [
                            'step' => 1,
                            'min'  => 0
                        ],
                        'required'   => TRUE,
                        'value'      => $entity->amount
                    ]),*/
               
                   /* field_render('product', [
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
                    ]),*/

                   /* field_render('sort', [
                        'type'  => 'number',
                        'label' => 'Порядок сортировки',
                        'value' => $entity->exists ? $entity->sort : 0,

                    ]),*/
                    /*field_render('status', [
                        'type'     => 'checkbox',
                        'selected' => $entity->exists ? $entity->status : 1,
                        'values'   => [
                            1 => 'Опубликовано'
                        ]
                    ])*/
                ],
            ],
        ];

        return $_form;
    }


    protected function _items($_wrap)
    {

       // print_r($_wrap);
       // exit();

        $this->__filter();
        $_filter = $this->filter;
        if ($this->filter_clear) {
            return redirect()
                ->route("oleus.{$this->base_route}");
        }
        $_filters = [];
        $_items = collect([]);

        $_user = Auth::user();

        $_query = Stock::orderByDesc('date_to')
        ->distinct()
        ->select([
            '*'
        ])
        ->paginate($this->entity->getPerPage(), ['id']);

       

   /*
        $iiko = new IikoClient(config('iiko-biz'));

        echo"=1====";
        //print_r($iiko->getToken());
        $organization = $iiko->OrganizationsApi()->getList()[0];
        print_r($organization);
        echo"=2====";
        // /api/0/organization/organizationId?access_token={accessToken}&request_timeout={requestTimeout}

        //print_r($_query);
        exit();    
*/

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
                'class' => 'uk-text-small uk-text-center',
                'style' => 'width: 40px;',
                'data'  => 'ID',
            ],
            [
                'data' => 'Название',
            ],
            [
                'data'  => 'Промо код',
            ],
            [
                'style' => 'width: 120px;',
                'data'  => 'Тип',
            ],
            [
                'data'  => 'Детали',
            ],
            [
                'style' => 'width: 120px;',
                'class' => 'uk-text-small uk-text-center',
                'data'  => 'Дата (до)',
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

//            print_r(Stock::type('all_busket'));
          //  exit();

            $_items = $_query->map(function ($_item) use ($_user) {
                $_response = [
                   "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                   $_item->title,
                   $_item->code,
                   Stock::type($_item->type),
                   
                   $_item->details,
                   $_item->date_to,              
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
    {    //        print_r( $request);
        //    exit();

 /*       if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
        }*/
        $_save = $request->only([
            'title',
            'type',
            'code',
            'date_to',
            'status'
        ]);
        $this->validate($request, [
            'title'       => 'required',
            'code'       => 'required',
            'date_to'       => 'required'
        ], [], [
            'title'       => 'Название',
            'code'       => 'Код скидки',
            'date_to'       => 'Дата'
        ]);
//        $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
        $_save['status'] = (int)($_save['status'] ?? 0);
/*        if ($_save['type'] == 'product') {
            $_save['product_id'] = $request->input('product.value');
        }*/
        $_item = Stock::updateOrCreate([
            'id' => NULL
        ], $_save);
/*
        Session::forget([
            'preview_fid',
        ]);*/

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, Stock $_item)
    {
      //  print_r($request);
      //  exit();
/*
        if ($preview_fid = $request->input('preview_fid')) {
            $_preview_fid = array_shift($preview_fid);
            Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
        }
*/

        $this->validate($request, [
            'title'       => 'required',
            'code'       => 'required',
            'date_to'       => 'required'
        ], [], [
            'title'       => 'Название',
            'code'       => 'Код скидки',
            'date_to'       => 'Дата'
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
                'code',
                'type',
                'date_to',
                'status'
            ]);

 //           $_save['preview_fid'] = $_preview_fid['id'] ?? NULL;
           $_save['status'] = (int)($_save['status'] ?? 0);

 /*
            if ($_save['type'] == 'product') {
                $_save['product_id'] = $request->input('product.value');
            }
            */

            app()->setLocale($_locale);
            $_item->update($_save);
        }
        /*
        Session::forget([
            'preview_fid',
        ]);
*/
        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Brand $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function product(Request $request)
    {//print_r($request);
       // exit();
        
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