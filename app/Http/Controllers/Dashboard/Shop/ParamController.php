<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Library\BaseController;
use App\Models\Shop\Param;
use App\Models\Shop\ParamItem;
use App\Models\Shop\Product;
use App\Models\Seo\UrlAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Validator;

class ParamController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Список параметров',
            'create'    => 'Добавить параметр',
            'edit'      => 'Редактировать параметр "<strong>:title</strong>"',
            'translate' => 'Перевод параметра на "<strong>:locale</strong>"',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:shop_params_read'
        ]);
        $this->base_route = 'shop_params';
        $this->permissions = [
            'read'   => 'shop_params_read',
            'create' => 'shop_params_create',
            'update' => 'shop_params_update',
            'delete' => 'shop_params_delete'
        ];
        $this->entity = new Param();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_form_fields = [
            field_render('locale', [
                'type'  => 'hidden',
                'value' => $this->defaultLocale,
            ]),
            field_render('title', [
                'label'      => 'Название параметра',
                'value'      => $entity->getTranslation('title', $this->defaultLocale),
                'required'   => TRUE,
                'attributes' => [
                    'autofocus' => TRUE,
                ],
            ]),
        ];
        if (!$entity->exists) {
            $_form_fields[] = field_render('name', [
                'label' => 'Машинное имя параметра',
                'value' => $entity->name,
                'help'  => 'Используется для обозначения параметра в <span class="uk-text-bold uk-text-primary">URL</span>.<br>При заполнении можно использовать символы латиского алфавита и знак подчеркивания. Ключ должен быть уникальным. Если поле оставить пустым будет сгенерирован ключ из названия параметра.<br><span class="uk-text-danger">Учтите, что значение поля в дальнейшем отредактировать нельзя!</span>'
            ]);
            $_form_fields[] = field_render('type', [
                'type'  => 'hidden',
                'value' => 'select'
            ]);
                            $_form_fields[] = field_render('type', [
                                'type'   => 'select',
                                'label'  => 'Тип',
                                'value'  => 'select',
                                'values' => [
                                    'select'       => 'Список элементов',
                                    'input_number' => 'Числовое поле',
                                    'input_text'   => 'Текстовое поле',
                                ],
                                'class'  => 'uk-select2'
                            ]);
        } else {
            $_form_fields[] = field_render('name', [
                'label'      => 'Машинное имя параметра',
                'value'      => $entity->name,
                'attributes' => [
                    'disabled' => TRUE
                ]
            ]);
        }
        $_form_fields[] = '<hr class="uk-divider-icon">';
        $_form_fields[] = '<div class="uk-child-width-1-2" uk-grid><div>';
        $_form_fields[] = field_render('teaser_title', [
            'label' => 'Название параметра в представлении товара',
            'value' => $entity->getTranslation('teaser_title', $this->defaultLocale),
        ]);
        $_form_fields[] = '</div><div>';
        $_form_fields[] = field_render('seo_title', [
            'label' => 'Название параметра при использовании в SEO',
            'value' => $entity->getTranslation('seo_title', $this->defaultLocale),
        ]);
        $_form_fields[] = '</div></div>';
        $_form_fields[] = field_render('visible_in_teaser', [
            'type'     => 'checkbox',
            'selected' => $entity->exists ? $entity->visible_in_teaser : 0,
            'values'   => [
                1 => 'Показывать в представлении товара'
            ]
        ]);
        $_form->tabs = [
            [
                'title'   => 'Основные параметры',
                'content' => $_form_fields,
            ],
        ];
        if ($entity->exists) {
            $_field_params = NULL;
            if ($entity->type == 'select') {
                $items = $entity->_items;
                $_field_params .= view('backend.partials.shop.param_item.items', compact('items', 'entity'));
            } elseif ($entity->type == 'input_number') {
                $_item = $entity->_relation_item;
                $_field_params = view('backend.partials.shop.param_item.input_number_item', compact('entity', '_item'))
                    ->render();
            } elseif ($entity->type == 'input_text') {
                $item = ParamItem::where('param_id', $entity->id)
                    ->first();
                $_field_params = view('backend.partials.shop.param_item.input_text_item', [
                    'param' => $entity,
                    'item'  => $item
                ])
                    ->render();
            }
            $_form->tabs[] = [
                'title'   => 'Настройки',
                'content' => [
                    $_field_params
                ]
            ];
        }

        return $_form;
    }

    protected function _form_translate($entity, $locale)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, [
            'translate' => $this->permissions['update']
        ]);
        $_field_unit = NULL;
        $_form->use_multi_language = FALSE;
        if ($entity->type != 'select') {
            $_option = $entity->_relation_item;
            $_field_unit = field_render('unit_value', [
                'label' => 'Ед. измерения',
                'value' => $_option->getTranslation('unit_value', $locale),
            ]);
        }
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
                    'label'      => 'Название параметра',
                    'value'      => $entity->getTranslation('title', $locale),
                    'required'   => TRUE,
                    'attributes' => [
                        'autofocus' => TRUE,
                    ],
                ]),
                field_render('teaser_title', [
                    'label' => 'Название параметра в представлении товара',
                    'value' => $entity->getTranslation('teaser_title', $locale),
                ]),
                $_field_unit
            ]
        ];

        return $_form;
    }

    protected function _items($_wrap)
    {
        $_items = collect([]);
        $_user = Auth::user();
        $_query = Param::orderBy('id')
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
                'data' => 'Название',
            ],
            [
                'data'  => 'Тип параметра',
                'class' => 'uk-width-medium',
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
                $_type = NULL;
                switch ($_item->type) {
                    case 'input_number':
                        $_type = 'Числовое поле';
                        break;
                    case 'input_text':
                        $_type = 'Текстовое поле';
                        break;
                    default:
                        $_type = 'Поле выбора';
                        break;
                }
                $_response = [
                    "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                    $_item->getTranslation('title', $this->defaultLocale),
                    $_type
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
        if (!$request->get('name')) $request->request->remove('name');
        $this->validate($request, [
            'title' => 'required',
            'name'  => 'sometimes|required|unique:shop_params|regex:/^[a-zA-Z0-9_-]+$/u',
        ], [], [
            'title' => 'Название параметра',
            'name'  => 'Машинное имя параметра',
        ]);
        $_save = $request->only([
            'title',
            'teaser_title',
            'seo_title',
            'name',
            'import_name',
            'type',
            'visible_in_teaser',
        ]);
        if (!isset($_save['name'])) {
            $_save['name'] = str_slug($_save['title'], '_');
            if (Param::where('name', $_save['name'])
                    ->count() > 0
            ) {
                $index = 0;
                while ($index <= 100) {
                    $_generate_name_index = "{$_save['name']}_{$index}";
                    if (self::where('alias', $_generate_name_index)
                            ->count() == 0
                    ) {
                        $_save['name'] = $_generate_name_index;
                        break;
                    }
                    $index++;
                }
            }
        }
        $_save['visible_in_teaser'] = (int)($_save['visible_in_teaser'] ?? 0);
        $_item = Param::updateOrCreate([
            'id' => NULL
        ], $_save);
        if ($_item->type != 'select') {
            if (ParamItem::where('name', $_save['name'])
                    ->count() > 0
            ) {
                $index = 0;
                while ($index <= 100) {
                    $_generate_name_index = "{$_save['name']}_{$index}";
                    if (self::where('alias', $_generate_name_index)
                            ->count() == 0
                    ) {
                        $_save['name'] = $_generate_name_index;
                        break;
                    }
                    $index++;
                }
            }
            ParamItem::updateOrCreate([
                'param_id' => $_item->id
            ], [
                'param_id' => $_item->id,
                'title'    => $_save['title'],
                'name'     => $_save['name'],
            ]);
        }

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, Param $_item)
    {
        $this->validate($request, [
            'title' => 'required',
        ], [], [
            'title' => 'Название параметра',
        ]);
        $_locale = $request->get('locale', config('app.default_locale'));
        $_translate = $request->get('translate', 0);
        if ($_translate) {
            $_save = $request->only([
                'title',
                'teaser_title',
                'seo_title',
            ]);
            foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
            $_item->save();
            if ($_item->type != 'select' && ($_option = $_item->_relation_item)) {
                $_save = $request->get('unit_value');
                $_option->setTranslation('unit_value', $_locale, $_save);
                $_option->save();
            }
        } else {
            $_save = $request->only([
                'title',
                'import_name',
                'teaser_title',
                'seo_title',
                'visible_in_teaser'
            ]);
            app()->setLocale($_locale);
            $_save['visible_in_teaser'] = (int)($_save['visible_in_teaser'] ?? 0);
            $_item->update($_save);
            if ($_item->type != 'select' && ($_option = $_item->_relation_item)) {
                $_save = $request->get('param_item');
                $_option->update($_save);
            }
        }

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Param $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }

    public function item(Request $request, Param $entity, $action, ParamItem $item)
    {
        $commands = [];
        switch ($action) {
            case 'add':
            case 'edit':
                $_item = $item;
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content'     => view('backend.partials.shop.param_item.item_modal', compact('_item', 'entity'))
                            ->render(),
                        'classDialog' => 'uk-width-1-2'
                    ]
                ];
                break;
            case 'save':
                $_default_locale = config('app.default_locale');
                $_save = $request->only([
                    'param_id',
                    'title',
                    'sub_title',
                    'meta_title',
                    'name',
                    'sort',
                    'visible_in_filter',
                    'icon_fid',
                    'style_id',
                    'style_class',
                    'attribute',
                ]);

//                $_save['alias'] = str_slug($_save['title'][$this->defaultLocale], '-');
                if ($icon = $_save['icon_fid']) {
                    $_icon = array_shift($icon);
                    Session::flash('icon_fid', json_encode([f_get($_icon['id'])]));
                }
                if (!$request->get('name')) $request->request->remove('name');
                $validate_rules = [
                    "title.{$_default_locale}" => 'required',
                    'name'                     => 'sometimes|required|unique:shop_param_items|regex:/^[a-zA-Z0-9_-]+$/u'
                ];
                $validator = Validator::make($request->all(), $validate_rules, [], [
                    "title.{$_default_locale}" => 'Название элемента списка',
                    'name'                     => 'Машинное имя элемента списка'
                ]);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => '#modal-param-item-form-form input',
                        'data'   => 'uk-form-danger'
                    ]
                ];
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $message) {
                        $commands['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($field, 'modal-param-item-form'),
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
                    if (isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                    $_save['visible_in_filter'] = (int)($_save['visible_in_filter'] ?? 0);
                    if ($item->exists) {
                        $item->update($_save);
                    } else {
                        if (is_null($_save['name'])) {
                            $_save['name'] = str_slug($_save['title'][$this->defaultLocale], '_');
                            if (ParamItem::where('name', $_save['name'])
                                    ->count() > 0
                            ) {
                                $index = 0;
                                while ($index <= 100) {
                                    $_generate_name_index = "{$_save['name']}_{$index}";
                                    if (ParamItem::where('name', $_generate_name_index)
                                            ->count() == 0
                                    ) {
                                        $_save['name'] = $_generate_name_index;
                                        break;
                                    }
                                    $index++;
                                }
                            }
                        }
                        $item->fill($_save)
                            ->save();
                    }
                    Session::forget([
                        'icon_fid'
                    ]);
                    $items = $entity->_items()
                        ->orderBy('sort')
                        ->get();
                    $_items_output = view('backend.partials.shop.param_item.item', compact('items'))
                        ->render();
                    $commands['commands'][] = [
                        'command' => 'html',
                        'options' => [
                            'target' => '#list-param-select-items',
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
                $item->delete();
                $items = $entity->_items()
                    ->orderBy('sort')
                    ->get();
                if ($items->isNotEmpty()) {
                    $_items_output = view('backend.partials.shop.param_item.item', compact('items'))
                        ->render();
                } else {
                    $_items_output = '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>Список элементов пуст</div>';
                }
                $commands['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#list-param-select-items',
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

//    public function alias(Request $request)
//    {
//        $_items = [];
//        if ($_search = $request->input('search')) {
////            $_exists_id = $this->entity::pluck('product_id');
//
//            $_items = UrlAlias::from('url_alias as a')
//                ->with([
//                    'model'
//                ])
//                ->where('a.model_default_title', 'like', "%{$_search}%")
//                ->where('a.model_type', Product::class)
////                ->when($_exists_id, function ($query) use ($_exists_id) {
////                    $query->whereNotIn('a.model_id', $_exists_id);
////                })
//                ->limit(8)
//                ->get([
//                    'a.*',
//                ]);
//
//            if ($_items->isNotEmpty()) {
//                $_items = $_items->transform(function ($_item) {
//                    $_model = $_item->model;
//
//                    return [
//                        'name' => $_model->title,
//                        'view' => NULL,
//                        'data' => $_model->id
//                    ];
//
//                })->toArray();
//            }
//        }
//
//        return response($_items, 200);
//    }
//
//    public function alias(Request $request)
//    {
//        $items = [];
//        if($_search_string = $request->input('search')) {
//
////            $_items = Product::where('title', 'like', "%{$_search_string}%")
//////                ->limit(10)
//////                ->with([
//////                    '_alias'
//////                ])
////                ->get();
//
//            $_items = Product::where('title', 'like', '%search_string%')->with(['category' => function ($category) {
//
//            }])->get();
//            dd($_items);
//            if($_items->isNotEmpty()) {
//                $_items->each(function ($item) use (&$items) {
//                    if($item->category){
//                        $items[] = [
//                            'name' => $item->title,
//                            'data' => $item->_alias->id,
//                            'view' => $item->category->title
//                        ];
//                    }else{
//                        $items[] = [
//                            'name' => $item->title,
//                            'data' => $item->_alias->id
//                        ];
//                    }
//                });
//            }
//        }
//
//        return response($items, 200);
//    }

}
