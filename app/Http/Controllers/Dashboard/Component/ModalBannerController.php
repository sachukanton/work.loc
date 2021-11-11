<?php

namespace App\Http\Controllers\Dashboard\Component;

use App\Library\BaseController;
use App\Models\Components\Banner;
use App\Models\Components\ModalBanner;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ModalBannerController extends BaseController
{

    use Authorizable;

    public function __construct()
    {
        parent::__construct();
        $this->middleware([
            'permission:banners_read'
        ]);
        $this->base_route = 'modal_banners';
        $this->permissions = [
            'read'   => 'banners_read',
            'create' => 'banners_create',
            'update' => 'banners_update',
            'delete' => 'banners_delete',
        ];
        $this->titles = [
            'index'     => 'Список модальных баннеров',
            'create'    => 'Добавить баннер',
            'edit'      => 'Редактировать баннер "<strong>:title</strong>"',
            'translate' => 'Перевод баннер на "<strong>:locale</strong>"',
            'delete'    => '',
        ];
        $this->entity = new ModalBanner();
    }

    protected function _form($entity)
    {
        $_form = $this->__form();
        $_form->route_tag = $this->base_route;
        $_form->permission = array_merge($_form->permission, $this->permissions);
        $_form->tabs = [
            [
                'title'   => 'Основные параметры',
                'content' => [
                    field_render('locale', [
                        'type'  => 'hidden',
                        'value' => config('app.default_locale'),
                    ]),
                    field_render('background_pc', [
                        'type'     => 'file',
                        'label'    => 'Фоновое изображение PC',
                        'allow'    => 'jpg|jpeg|png',
                        'values'   => $entity->exists && $entity->getTranslation('background_pc', $this->defaultLocale) ? [f_get($entity->getTranslation('background_pc', $this->defaultLocale))] : NULL,
                        'required' => TRUE
                    ]),
                    field_render('background_mob', [
                        'type'     => 'file',
                        'label'    => 'Фоновое изображение MOBILE',
                        'allow'    => 'jpg|jpeg|png',
                        'values'   => $entity->exists && $entity->getTranslation('background_mob', $this->defaultLocale) ? [f_get($entity->getTranslation('background_mob', $this->defaultLocale))] : NULL,
                        'required' => TRUE
                    ]),
                    '<h3 class="uk-heading-line uk-text-uppercase uk-margin-remove-top"><span>Ссылка для перехода</span></h3>',
                    field_render('link', [
                        'label' => 'Ссылка для перехода по клику',
                        'value' => $entity->getTranslation('link', $this->defaultLocale),
                    ]),
                    field_render('link_attributes', [
                        'type'       => 'textarea',
                        'label'      => 'Дополнительные атрибуты',
                        'value'      => $entity->link_attributes,
                        'attributes' => [
                            'rows' => 2,
                        ]
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
                field_render('background_pc', [
                    'type'     => 'file',
                    'label'    => 'Фоновое изображение PC',
                    'allow'    => 'jpg|jpeg|png',
                    'values'   => $entity->exists && $entity->getTranslation('background_pc', $locale) ? [f_get($entity->getTranslation('background_pc', $locale))] : NULL,
                    'required' => TRUE
                ]),
                field_render('background_mob', [
                    'type'     => 'file',
                    'label'    => 'Фоновое изображение MOBILE',
                    'allow'    => 'jpg|jpeg|png',
                    'values'   => $entity->exists && $entity->getTranslation('background_mob', $locale) ? [f_get($entity->getTranslation('background_mob', $locale))] : NULL,
                    'required' => TRUE
                ]),
                field_render('link', [
                    'label'    => 'Ссылка для перехода по клику',
                    'value'    => $entity->getTranslation('link', $locale),
                    'required' => TRUE
                ])
            ]
        ];

        return $_form;
    }

    protected function _items($_wrap)
    {
        $_user = Auth::user();
        $_items = collect([]);
        $_query = ModalBanner::orderByDesc('status')
            ->paginate();
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
                'class' => 'uk-width-medium',
                'data'  => 'PC',
            ],
            [
                'class' => 'uk-width-medium',
                'data'  => 'MOBILE',
            ],
            [
                'data' => 'Ссылка',
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
        $_languages = config('laravellocalization.supportedLocales');
        if ($_query->isNotEmpty()) {
            $_items = $_query->map(function ($_item) use ($_user, $_languages) {
                $_links = [];
                foreach ($_languages as $_lang => $_data) {
                    $_links[] = '<span class="uk-text-uppercase uk-text-primary">' . $_lang . '</span> - ' . $_item->getTranslation('link', $_lang);
                }
                $_response = [
                    "<div class='uk-text-center uk-text-bold'>{$_item->id}</div>",
                    image_render($_item->_background_pc, 'thumb_100'),
                    image_render($_item->_background_mobile, 'thumb_100'),
                    implode('<br>', $_links),
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
        if ($background_pc = $request->input('background_pc')) {
            $_background_pc = array_shift($background_pc);
            Session::flash('background_pc', json_encode([f_get($_background_pc['id'])]));
        }
        if ($background_mob = $request->input('background_mob')) {
            $_background_mob = array_shift($background_mob);
            Session::flash('background_mob', json_encode([f_get($_background_mob['id'])]));
        }
        $this->validate($request, [
            'background_pc'  => 'required',
            'background_mob' => 'required',
        ], [], [
            'background_pc'  => 'Фоновое изображение PC',
            'background_mob' => 'Фоновое изображение MOBILE',
        ]);
        $_save = $request->only([
            'link',
            'link_attributes',
            'status',
            'background_pc',
            'background_mob',
        ]);
        $_save['background_pc'] = $_background_pc['id'] ?? NULL;
        $_save['background_mob'] = $_background_mob['id'] ?? NULL;
        $_save['status'] = (int)($_save['status'] ?? 0);
        $_item = ModalBanner::updateOrCreate([
            'id' => NULL
        ], $_save);
        Session::forget([
            'background_pc',
            'background_mob',
        ]);

        return $this->__response_after_store($request, $_item);
    }

    public function update(Request $request, ModalBanner $_item)
    {
        if ($background_pc = $request->input('background_pc')) {
            $_background_pc = array_shift($background_pc);
            Session::flash('background_pc', json_encode([f_get($_background_pc['id'])]));
        }
        if ($background_mob = $request->input('background_mob')) {
            $_background_mob = array_shift($background_mob);
            Session::flash('background_mob', json_encode([f_get($_background_mob['id'])]));
        }
        $_locale = $request->get('locale', config('app.default_locale'));
        $_translate = $request->get('translate', 0);
        if ($_translate) {
            $_save = $request->only([
                'link',
                'background_pc',
                'background_mob',
            ]);
            $_save['background_pc'] = $_background_pc['id'] ?? NULL;
            $_save['background_mob'] = $_background_mob['id'] ?? NULL;
            foreach ($_save as $_key => $_value) $_item->setTranslation($_key, $_locale, $_value);
            $_item->save();
        } else {
            $this->validate($request, [
                'background_pc'  => 'required',
                'background_mob' => 'required',
            ], [], [
                'background_pc'  => 'Фоновое изображение PC',
                'background_mob' => 'Фоновое изображение MOBILE',
            ]);
            $_save = $request->only([
                'link',
                'link_attributes',
                'status',
                'background_pc',
                'background_mob',
            ]);
            $_save['background_pc'] = $_background_pc['id'] ?? NULL;
            $_save['background_mob'] = $_background_mob['id'] ?? NULL;
            $_save['status'] = (int)($_save['status'] ?? 0);
            $_item->update($_save);
        }
        Session::forget([
            'background_pc',
            'background_mob',
        ]);

        return $this->__response_after_update($request, $_item);
    }

    public function destroy(Request $request, Banner $_item)
    {
        $_item->delete();

        return $this->__response_after_destroy($request, $_item);
    }
}
