<?php
namespace App\Http\Controllers\Dashboard\Shop;

//namespace Iiko\Biz;

use App\Library\BaseController;
use App\Models\Seo\UrlAlias;
use App\Models\Shop\Category;
//use Illuminate\Database\Eloquent\Collection;
//use App\Models\Shop\Brand;
//use App\Models\Shop\Gift;
use App\Models\Shop\Product;

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

        /** Selection of product categories */
        $_categories = Category::tree_parents();
        if ($_categories->isNotEmpty()) {
            $_categories = $_categories->map(function ($_item) {
                return $_item['title_option'];
            });
        }
        $_categories->prepend('-- Выбрать --', '');

        if(!empty($entity->details)){
            $entity->discount   = json_decode($entity->details)->discount ?? 0;
            $entity->categories = json_decode($entity->details)->categories ?? 0;
            $entity->products   = json_decode($entity->details)->products ?? 0;
        }

        $_products = [];
        if(!empty($entity->categories))  $_products = Stock::_getProducts($entity->categories);

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
                    field_render('type', [
                        'type'     => 'radio',
                        'class'      => 'stock_type',
                        'selected' => $entity->exists ? $entity->type : 0,
                        'values'   => Stock::type()
                    ]),
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
                   
                    field_render('categories', [
                        'type'     => 'select',
                        'label'      => 'Доступные категории',
                        'class'      => 'uk-select2 stock_categories',
                        'required'   => TRUE,
                        'selected' => $entity->categories,
                        'values'   => $_categories,
                        'options' => 'data-minimum-results-for-search="5"'
                    ]),
                    field_render('products', [
                        'type'     => 'select',
                        'label'      => 'Продукция',
                        'class'      => 'uk-select2 stock_products',
                        'required'   => TRUE,
                        'selected' => $entity->products,
                        'values'   => $_products,
                        'options' => 'data-minimum-results-for-search="5"'
                    ]),

                    field_render('discount', [
                        'label'      => 'Скидка, %',
                        'class'      => 'stock_discount',
                        'type'       => 'number',
                        'required'   => TRUE,
                        'value'      => $entity->discount,
                        'attributes' => [
                            'autofocus' => TRUE,
                            'min'  => 0,
                            'max'  => 100
                        ],
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
                ],
            ],
        ];

        return $_form;
    }


    protected function _items($_wrap)
    {
        $_items = collect([]);

        $_user = Auth::user();

        $_query = Stock::orderByDesc('id')
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

            $_items = $_query->map(function ($_item) use ($_user) {

                $detail = json_decode($_item->details);
                $detail_="";

                $detail_.=!empty($detail_) ? "<br>":"";
                $detail_.=!empty($detail->categories) ? "Категория: ".Category::where('id', $detail->categories)->pluck('title')[0]:"";

                $detail_.=!empty($detail_)&&!empty($detail->products) ? "<br>":"";
                $detail_.=!empty($detail->products) ? "Продукция: ".Product::where('id', $detail->products)->pluck('title')[0]:"";

                $detail_.=!empty($detail_) ? "<br>":"";
                $detail_.=!empty($detail->discount) ? "Скидка: ".$detail->discount."%":"";

                //echo"---".Category::where('id', $detail->categories)->pluck('title');

                $_response = [
                   "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                   $_item->title,
                   $_item->code,
                   Stock::type($_item->type),
                   $detail_,
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
            'items'       => $_items,
            'pagination'  => $_query->links('backend.partials.pagination')
        ]);

        return view('backend.partials.list_items', compact('_items', '_wrap'));
    }

    /** Add stock */
    public function store(Request $request)
    {   
        $type = $request->get("type",0);
        $discount = $request->get("discount",0);
        $categories = $type != 'all_basket' ? /*$type != 'product_null' ?*/ $request->get("categories",0) : '' /*: ''*/;
        $products =  $type != 'all_basket' ?  $request->get("products",0) : '';
 
        $_save = $request->only([
            'title',
            'code',
            'type',
            'date_to',
            'status'
        ]);

        $this->validate($request, [
            'title'      => 'required',
            'code'       => 'required',
            'date_to'    => 'required',
            'discount'   => $type != 'product_null' ? 'required|min:0|max:100|not_in:0' : '',
            'categories' => $type != 'all_basket' ? 'required' : "",
            'products'   => $type != 'all_basket' ? $type != 'sale_product' ? 'required' : '' : '',
        ], [], [
            'title'      => 'Название',
            'code'       => 'Код скидки',
            'date_to'    => 'Дата',
            'discount'   => 'Скидка',
            'categories' => 'Категории',
            'products'   => 'Продукция'
        ]);

        $_save['status'] = (int)($_save['status'] ?? 0);
        $_save['details'] = json_encode(array('discount' => $discount, 'categories' => $categories, 'products' => $products));

        $_item = Stock::updateOrCreate([
            'id' => NULL
        ], $_save);

        return $this->__response_after_store($request, $_item);
    }
     

    public function update(Request $request, Stock $_item)
    {
        $type = $request->get("type",0);
        $discount = $request->get("discount",0);
        $categories = $type != 'all_basket' ? /*$type != 'product_null' ?*/ $request->get("categories",0) : '' /*: ''*/;
        $products =  $type != 'all_basket' ?  $request->get("products",0) : '';
 
        $this->validate($request, [
            'title'      => 'required',
            'code'       => 'required',
            'date_to'    => 'required',
            'discount'   => $type != 'product_null' ? 'required|min:0|max:100|not_in:0' : '',
            'categories' => $type != 'all_basket' ? 'required' : "",
            'products'   => $type != 'all_basket' ? $type != 'sale_product' ? 'required' : '' : '',
        ], [], [
            'title'      => 'Название',
            'code'       => 'Код скидки',
            'date_to'    => 'Дата',
            'discount'   => 'Скидка',
            'categories' => 'Категории',
            'products'   => 'Продукция'
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

           $_save['status'] = (int)($_save['status'] ?? 0);

           $_save['details'] = json_encode(array('discount' => $discount, 'categories' => $categories, 'products' => $products));

            app()->setLocale($_locale);
            $_item->update($_save);
        }
        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Brand $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    /** Selection products depending on the category  */
    public function getproducts($cid = 0){
        if(!empty($cid)){
               echo Stock::getProducts($cid);
        }else  echo '';
    }
}