<?php

    namespace App\Http\Controllers\Callback;

    use App\Library\BaseController;
    use App\Models\File\File;
    use App\Models\Shop\Product;
    use App\Models\User\WishList;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\View;

    class AccountController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function profile_edit(Request $request)
        {
            $_user = Auth::user();
            $_response = [
                'result'   => FALSE,
                'message'  => NULL,
                'commands' => NULL,
            ];
            $_form = $request->get('form_id');
            $_response['commands'][] = [
                'command' => 'removeClass',
                'options' => [
                    'target' => "#{$_form} *",
                    'data'   => 'error'
                ]
            ];
            $_validate_rules = [
                'email'   => 'required|string|email|max:255|unique:users,email,' . $_user->id,
                'name'    => 'required|string',
                'phone'   => 'required|string|phoneNumber|phoneOperatorCode',
                'captcha' => 'required|reCaptchaV3',
            ];
            if ($request->get('password_change')) {
                $_validate_rules['password'] = 'required|string|min:8|confirmed';
            }
            $_validator = Validator::make($request->all(), $_validate_rules, [], [
                'email'    => trans('forms.fields.profile.email'),
                'password' => trans('forms.fields.profile.password'),
                'name'     => trans('forms.fields.profile.name'),
                'phone'    => trans('forms.fields.profile.phone'),
                'captcha'  => trans('forms.fields.captcha'),
            ]);
            if ($_validator->fails()) {
                foreach ($_validator->errors()->messages() as $_field => $_message) {
                    $_response['message'] .= "<div>{$_message[0]}</div>";
                    $_response['commands'][] = [
                        'command' => 'addClass',
                        'options' => [
                            'target' => '#' . generate_field_id($_field, $_form),
                            'data'   => 'error'
                        ]
                    ];
                    if ($_field == 'password') {
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($_field, $_form) . '-confirmation',
                                'data'   => 'error'
                            ]
                        ];
                    }
                }
                $_response['commands'][] = [
                    'command' => 'val',
                    'options' => [
                        'target' => "#{$_form} input[type='password']",
                        'data'   => ''
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => $_response['message'],
                        'status' => 'danger'
                    ]
                ];
            } else {
                $_profile = $_user->_profile;
                $_profile->name = $request->input('name');
                $_profile->surname = $request->input('surname');
                $_profile->company = $request->input('company');
                $_profile->phone = $request->input('phone');
                $_profile->avatar_fid = $request->input('avatar');
                if (!$request->input('avatar') && $request->hasFile('file')) {
                    $_new_avatar = NULL;
                    try {
                        $_file = $request->file('file');
                        $_file_size = $_file->getClientSize();
                        $_file_base_name = $_file->getClientOriginalName();
                        $_file_mime_type = $_file->getClientMimeType();
                        $_file_extension = $_file->getClientOriginalExtension();
                        $_file_name = str_slug(basename($_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                        Storage::disk('public')
                            ->put($_file_name, file_get_contents($_file->getRealPath()));
                        $_new_avatar = File::updateOrCreate([
                            'id' => NULL
                        ], [
                            'base_name' => $_file_base_name,
                            'filename'  => $_file_name,
                            'filemime'  => $_file_mime_type,
                            'filesize'  => $_file_size,
                        ]);
                    } catch (\Exception $exception) {
                    }
                    if ($_new_avatar) $_profile->avatar_fid = $_new_avatar->id;
                }
                $_profile->save();
                $_user->email = $request->input('email');
                if ($request->get('password_change')) $_user->password = Hash::make($request->input('password'));
                $_user->save();
                $_response['result'] = TRUE;
                $_response['message'] = trans('forms.messages.profile.message');
            }

            return response($_response, 200);
        }

        public function add_wish_list(Request $request, Product $product = NULL)
        {
            $_user = Auth::user();
            if ($product) {
                $_item = new WishList();
                $_item->fill([
                    'name' => 'Новый список'
                ]);
                $_user->_wish_lists()->save($_item);
                $_item->_products()->attach($product);
                $_output = NULL;
                foreach ($_user->_wish_lists as $_list) {
                    $_output .= "<li><a href=\"" . _r('ajax.add_product_in_wish_list', [
                            'list'    => $_list->id,
                            'product' => $product->id
                        ]) . "\" rel=\"nofollow\" class=\"use-ajax\">{$_list->name}</a></li>";
                }
                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '.wish-list-dropdown-menu',
                        'data'   => clear_html($_output)
                    ]
                ];
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => trans('forms.messages.wish_list.add_product_in_new_list'),
                        'status' => 'success'
                    ]
                ];
            } else {
                $_form = $request->get('form_id');
                $_response = [
                    'result'   => FALSE,
                    'message'  => NULL,
                    'commands' => NULL,
                ];
                $_validate_rules = [
                    'name'    => 'required|string',
                    'captcha' => 'required|reCaptchaV3',
                ];
                $_validator = Validator::make($request->all(), $_validate_rules, [], [
                    'name'    => trans('forms.fields.wish_list.name'),
                    'captcha' => trans('forms.fields.captcha'),
                ]);
                $_response['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => "#{$_form} *",
                        'data'   => 'error'
                    ]
                ];
                if ($_validator->fails()) {
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $_response['message'] .= "<div>{$_message[0]}</div>";
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id($_field, $_form),
                                'data'   => 'error'
                            ]
                        ];
                    }
                    $_response['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => $_response['message'],
                            'status' => 'danger'
                        ]
                    ];
                } else {
                    $_name = $request->get('name');
                    $_wish_lists = $_user->_wish_lists();
                    $_exist = $_wish_lists->where('name', $_name)
                        ->first();
                    if ($_exist) {
                        $_response['message'] .= '<div>' . trans('forms.messages.wish_list.name_exists') . '</div>';
                        $_response['commands'][] = [
                            'command' => 'addClass',
                            'options' => [
                                'target' => '#' . generate_field_id('name', $_form),
                                'data'   => 'error'
                            ]
                        ];
                        $_response['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => $_response['message'],
                                'status' => 'danger'
                            ]
                        ];
                    } else {
                        $_item = new WishList();
                        $_item->fill([
                            'name' => $request->get('name')
                        ]);
                        $_user->_wish_lists()->save($_item);
                        $_response['commands'][] = [
                            'command' => 'clearForm',
                            'options' => [
                                'target' => "#{$_form}"
                            ]
                        ];
                        $_response['commands'][] = [
                            'command' => 'eval',
                            'options' => [
                                'data' => "$('#collapse-add-new-list').collapse('toggle');"
                            ]
                        ];
                        $_user = Auth::user();
                        $_item = new \stdClass();
                        $_item->_items = $_user->_wish_lists;
                        $_response['commands'][] = [
                            'command' => 'replaceWith',
                            'options' => [
                                'target' => '#wish-list-items',
                                'data'   => View::first([
                                    "frontend.{$this->deviceTemplate}.user.account.wish_list_items",
                                    'frontend.default.user.account.wish_list_items'
                                ], compact('_item'))
                                    ->render(function ($view, $_content) {
                                        return clear_html($_content);
                                    })
                            ]
                        ];
                    }
                }
            }

            return response($_response, 200);
        }

        public function remove_wish_list(Request $request, WishList $list)
        {
            $list->delete();
            $_response['commands'][] = [
                'command' => 'remove',
                'options' => [
                    'target' => "#wish-list-item-{$list->id}"
                ],
            ];
            $_user = Auth::user();
            if ($_user->_wish_lists->count() == 0) {
                $_response['commands'][] = [
                    'command' => 'html',
                    'options' => [
                        'target' => '#wish-list-items',
                        'data'   => '<div class="alert alert-warning">' . trans('frontend.you_have_no_wish_lists') . '</div>'
                    ],
                ];
            }

            return response($_response, 200);
        }

        public function rename_wish_list(Request $request, WishList $list)
        {
            $list->update([
                'name' => $request->get('name')
            ]);

            return response([], 200);
        }

        public function add_product_in_wish_list(Request $request, WishList $list, Product $product)
        {
            $_products = $list->_products->keyBy('id');
            if ($_products->has($product->id)) {
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => trans('forms.messages.wish_list.product_is_already_on_list'),
                        'status' => 'warning'
                    ]
                ];
            } else {
                $list->_products()->attach($product);
                $_response['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => trans('forms.messages.wish_list.add_product_in_list'),
                        'status' => 'success'
                    ]
                ];
            }

            return response($_response, 200);
        }

        public function remove_product_in_wish_list(Request $request, WishList $list, Product $product)
        {
            $list->_products()->detach($product->id);
            $_response['commands'][] = [
                'command' => 'remove',
                'options' => [
                    'target' => "#wish-list-item-{$list->id}-product-{$product->id}"
                ]
            ];

            return response($_response, 200);
        }

        public function comment_to_product_wish_list(Request $request, WishList $list, Product $product)
        {
            $_comment = $request->get('data');
            $list->_products()->updateExistingPivot($product->id, ['comment' => $_comment]);

            return response([], 200);
        }

    }
