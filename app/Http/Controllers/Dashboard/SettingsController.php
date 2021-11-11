<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Library\ConfigFileSave;
use App\Library\Dashboards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use function foo\func;

class SettingsController extends Controller
{

    use Dashboards;

    protected $form_theme = 'backend.forms.form_settings';

    public function __construct()
    {
        parent::__construct();
        $this->middleware([
            'permission:settings_read'
        ]);
        $this->notice = [
            'save_settings'  => 'Настройки сохранены',
            'save_translate' => 'Перевод сохранен',
        ];
    }

    public function _view(Request $request, $method)
    {
        if (method_exists($this, $method)) return $this->callAction($method, [$request]);

        return redirect()
            ->back()
            ->with('notice', [
                'message' => 'Настройки не найдены',
                'status'  => 'warning'
            ]);
    }

    public function _translate(Request $request, $method, $action)
    {
        $commands = [];
        switch ($action) {
            case 'edit':
                switch ($method) {
                    case 'overall':
                        $commands['commands'][] = [
                            'command' => 'UK_modal',
                            'options' => [
                                'content'     => view('backend.partials.settings.overall_modal')
                                    ->render(),
                                'classDialog' => 'uk-width-1-2'
                            ]
                        ];
                        break;
                    case 'seo':
                        $commands['commands'][] = [
                            'command' => 'UK_modal',
                            'options' => [
                                'content'     => view('backend.partials.settings.seo_modal')
                                    ->render(),
                                'classDialog' => 'uk-width-1-2'
                            ]
                        ];
                        break;
                    case 'currency':
                        $commands['commands'][] = [
                            'command' => 'UK_modal',
                            'options' => [
                                'content'     => view('backend.partials.settings.currency_modal')
                                    ->render(),
                                'classDialog' => 'uk-width-1-2'
                            ]
                        ];
                        break;
                    case 'contacts':
                        $commands['commands'][] = [
                            'command' => 'UK_modal',
                            'options' => [
                                'content'     => view('backend.partials.settings.contacts_modal')
                                    ->render(),
                                'classDialog' => 'uk-width-1-2'
                            ]
                        ];
                        break;
                }
                break;
            case 'save':
                switch ($method) {
                    case 'overall':
                    case 'seo':
                        $_save = $request->except('save');
                        ConfigFileSave::set('os_seo', array_dot($_save));
                        $commands['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => $this->notice['save_translate'],
                                'status' => 'success',
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'UK_modalClose',
                            'options' => []
                        ];
                        break;
                    case 'currency':
                        $_save = $request->except('save');
                        ConfigFileSave::set('os_currencies', array_dot($_save));
                        $commands['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => $this->notice['save_translate'],
                                'status' => 'success',
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'UK_modalClose',
                            'options' => []
                        ];
                        break;
                    case 'contacts':
                        $_save = $request->except('save');
                        ConfigFileSave::set('os_contacts', array_dot($_save));
                        $commands['commands'][] = [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => $this->notice['save_translate'],
                                'status' => 'success',
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'UK_modalClose',
                            'options' => []
                        ];
                        break;
                }
                break;
        }
        if (!count($commands)) {
            $commands['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'status' => 'danger',
                    'text'   => 'Ошибка! Что-то пошло не так.'
                ]
            ];
        }

        return response($commands, 200);
    }

    public function seo(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Найстройка SEO параметров'
        ]);
        $_item = config('os_seo');
        $_default_locale = config('app.default_locale');
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'seo';
            $_form->tabs = [
                [
                    'title'   => 'Настройки',
                    'content' => [
                        field_render("settings.{$_default_locale}.description", [
                            'type'       => 'textarea',
                            'label'      => 'Description (по умолчанию)',
                            'value'      => config_data_load($_item, 'settings.*.description', $_default_locale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render("settings.{$_default_locale}.keywords", [
                            'type'       => 'textarea',
                            'label'      => 'Keywords (по умолчанию)',
                            'value'      => config_data_load($_item, 'settings.*.keywords', $_default_locale),
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                        field_render('robots', [
                            'type'   => 'select',
                            'label'  => 'Robots',
                            'value'  => $_item['robots'],
                            'values' => [
                                'index, follow'     => 'index, follow',
                                'noindex, follow'   => 'noindex, follow',
                                'index, nofollow'   => 'index, nofollow',
                                'noindex, nofollow' => 'noindex, nofollow'
                            ],
                            'class'  => 'uk-select2'
                        ]),
                        field_render("settings.{$_default_locale}.suffix_title", [
                            'label' => 'Окончание в заголовке',
                            'value' => config_data_load($_item, 'settings.*.suffix_title', $_default_locale),
                        ]),
                        field_render("settings.{$_default_locale}.copyright", [
                            'label' => 'Копирайт в &lt;head&gt;',
                            'value' => config_data_load($_item, 'settings.*.copyright', $_default_locale),
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('use.last_modified', [
                            'type'     => 'checkbox',
                            'selected' => $_item['use']['last_modified'],
                            'values'   => [
                                1 => 'Включить last modified для FRONTEND'
                            ]
                        ]),
                        field_render('use.compress', [
                            'type'     => 'checkbox',
                            'selected' => $_item['use']['compress'],
                            'values'   => [
                                1 => 'Очистить и сжать код HTML'
                            ]
                        ]),
                        field_render('use.block_scan', [
                            'type'     => 'checkbox',
                            'selected' => $_item['use']['block_scan'],
                            'values'   => [
                                1 => 'Заблокировать сканирование'
                            ]
                        ]),
                        field_render('use.multi_language', [
                            'type'     => 'checkbox',
                            'selected' => $_item['use']['multi_language'],
                            'values'   => [
                                1 => 'Включить мультиязычность'
                            ]
                        ]),
                    ]
                ],
                [
                    'title'   => 'ROBOTS.TXT',
                    'content' => [
                        field_render('robots_txt', [
                            'type'       => 'textarea',
                            'label'      => 'robots.txt',
                            'value'      => robots(),
                            'attributes' => [
                                'rows' => 20,
                            ]
                        ])
                    ]
                ]
            ];
            if (config('os_seo.use.multi_language')) {
                $_form->buttons[] = _l('Добавить перевод', 'oleus.settings.translate', [
                    'p'          => [
                        'setting' => 'seo',
                        'action'  => 'edit'
                    ],
                    'attributes' => [
                        'class' => 'uk-button uk-button-primary uk-margin-small-right uk-text-uppercase use-ajax'
                    ]
                ]);
            }

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        $_config = $request->only([
            'use',
            'settings',
            'path',
            'robots',
            'google_services_key',
            'analytics'
        ]);
        $_config['use']['last_modified'] = (int)($_config['use']['last_modified'] ?? 0);
        $_config['use']['compress'] = (int)($_config['use']['compress'] ?? 0);
        $_config['use']['block_scan'] = (int)($_config['use']['block_scan'] ?? 0);
        $_config['use']['multi_language'] = (int)($_config['use']['multi_language'] ?? 0);
        update_last_modified_timestamp();
        ConfigFileSave::set('os_seo', array_dot($_config));
        robots(TRUE);

        return redirect()
            ->route('oleus.settings', ['page' => 'seo'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

    public function overall(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Общие настройки'
        ]);
        $_item = config('os_seo');
        $_default_locale = config('app.locale');
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'overall';
            $_form->tabs = [
                [
                    'title'   => 'Настройки',
                    'content' => [
                        field_render("settings.{$_default_locale}.site_name", [
                            'label' => 'Название сайта',
                            'value' => config_data_load($_item, 'settings.*.site_name', $_default_locale),
                        ]),
                        field_render("settings.{$_default_locale}.site_slogan", [
                            'type'       => 'textarea',
                            'label'      => 'Слоган сайта',
                            'value'      => config_data_load($_item, 'settings.*.site_slogan', $_default_locale),
                            'attributes' => [
                                'rows' => 2,
                            ]
                        ]),
                        field_render("settings.{$_default_locale}.site_copyright", [
                            'label' => 'Копирайт в подвале',
                            'value' => config_data_load($_item, 'settings.*.site_copyright', $_default_locale),
                            'help'  => ':year - автоматически подставит текущий год'
                        ]),
                        '<hr class="uk-divider-icon">',
                        '<div class="uk-grid uk-child-width-1-2"><div>',
                        field_render('theme_color', [
                            'type'  => 'color',
                            'label' => 'Цвет брузера',
                            'icon'  => 'paint-bucket',
                            'value' => $_item['theme_color'],
                        ]),
                        '</div><div>',
                        field_render('page_class', [
                            'label' => 'CLASS &lt;body&gt; (по умолчанию)',
                            'value' => $_item['page_class'],
                        ]),
                        '</div></div>'
                    ],
                ],
                [
                    'title'   => 'Файлы',
                    'content' => [
                        '<div class="uk-grid uk-child-width-1-3 uk-margin"><div>',
                        field_render('logotype.top', [
                            'type'   => 'file',
                            'label'  => 'Логотип в верху сайта',
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $_item['logotype']['top'] ? [f_get($_item['logotype']['top'])] : NULL,
                        ]),
                        '</div><div>',
                        field_render('logotype.footer', [
                            'type'   => 'file',
                            'label'  => 'Логотип внизу сайта',
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $_item['logotype']['footer'] ? [f_get($_item['logotype']['footer'])] : NULL,
                        ]),
                        '</div><div>',
                        field_render('logotype.mobile', [
                            'type'   => 'file',
                            'label'  => 'Логотип для мобильной версии',
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $_item['logotype']['mobile'] ? [f_get($_item['logotype']['mobile'])] : NULL,
                        ]),
                        '</div></div>'
                    ]
                ]
            ];
            if (config('os_seo.use.multi_language')) {
                $_form->buttons[] = _l('Добавить перевод', 'oleus.settings.translate', [
                    'p'          => [
                        'setting' => 'overall',
                        'action'  => 'edit'
                    ],
                    'attributes' => [
                        'class' => 'uk-button uk-button-primary uk-margin-small-right uk-text-uppercase use-ajax'
                    ]
                ]);
            }

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        if ($logotype_first = $request->input('logotype.top')) {
            $_logotype_first = array_shift($logotype_first);
            $_logotype_first = f_save(f_get($_logotype_first['id']), $_logotype_first);
            Session::flash('logotype.top', json_encode([$_logotype_first]));
        }
        if ($logotype_last = $request->input('logotype.footer')) {
            $_logotype_last = array_shift($logotype_last);
            $_logotype_last = f_save(f_get($_logotype_last['id']), $_logotype_last);
            Session::flash('logotype.footer', json_encode([$_logotype_last]));
        }
        if ($logotype_mobile = $request->input('logotype.mobile')) {
            $_logotype_mobile = array_shift($logotype_mobile);
            $_logotype_mobile = f_save(f_get($_logotype_mobile['id']), $_logotype_mobile);
            Session::flash('logotype.mobile', json_encode([$_logotype_mobile]));
        }
        $_config = $request->only([
            'settings',
            'theme_color',
            'page_class',
            'favicon',
            'logotype',
        ]);
        if (isset($_logotype_first)) $_config['logotype']['top'] = (int)$_logotype_first['id'];
        if (isset($_logotype_last)) $_config['logotype']['footer'] = (int)$_logotype_last['id'];
        if (isset($_logotype_mobile)) $_config['logotype']['mobile'] = (int)$_logotype_mobile['id'];
        update_last_modified_timestamp();
        ConfigFileSave::set('os_seo', array_dot($_config));
        Session::forget([
            'logotype.first',
            'logotype.last',
            'logotype.mobile',
        ]);

        return redirect()
            ->route('oleus.settings', ['page' => 'overall'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

    public function contacts(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Контактная информация'
        ]);
        $_item = config('os_contacts');
        $_default_locale = config('app.locale');
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'contacts';
            $_form->tabs = [
                [
                    'title'   => 'Контакты',
                    'content' => [
                        '<div uk-grid>',
                        '<div class="uk-width-1-3">',
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Номера телефонов</span></h3>',
                        field_render('phones.0', [
                            'label'      => 'Телефон 1',
                            'value'      => $_item['phones'][0],
                            'attributes' => [
                                'class' => 'phone-mask uk-input'
                            ],
                        ]),
                        field_render('phones.1', [
                            'label'      => 'Телефон 2',
                            'value'      => $_item['phones'][1],
                            'attributes' => [
                                'class' => 'phone-mask uk-input'
                            ],
                        ]),
                        field_render('phones.2', [
                            'label'      => 'Телефон 3',
                            'value'      => $_item['phones'][2],
                            'attributes' => [
                                'class' => 'phone-mask uk-input'
                            ],
                        ]),
                        field_render('phones.3', [
                            'label'      => 'Телефон 4',
                            'value'      => $_item['phones'][3],
                            'attributes' => [
                                'class' => 'phone-mask uk-input'
                            ],
                        ]),
                        field_render('phones.4', [
                            'label'      => 'Телефон 5',
                            'value'      => $_item['phones'][4],
                            'attributes' => [
                                'class' => 'phone-mask uk-input'
                            ],
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Электронная почта</span></h3>',
                        field_render('email', [
                            'label' => 'E-mail',
                            'value' => $_item['email'],
                        ]),
                        '</div><div class="uk-width-2-3">',
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Время работы</span></h3>',
                        field_render("working_hours.{$_default_locale}", [
                            'type'  => 'textarea',
                            'label' => 'Время работы',
                            'value' => $_item['working_hours'][$_default_locale],
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Адрес</span></h3>',
                        field_render("address.{$_default_locale}", [
                            'type'  => 'textarea',
                            'label' => 'Юридический адрес',
                            'value' => $_item['address'][$_default_locale],
                        ]),
                        '<div uk-grid class="uk-child-width-1-2">',
                        '<div>',
                        field_render('locations.lat', [
                            'label' => 'Широта',
                            'value' => $_item['locations']['lat'],
                        ]),
                        '</div><div>',
                        field_render('locations.lng', [
                            'label' => 'Долгота',
                            'value' => $_item['locations']['lng'],
                        ]),
                        '</div></div></div></div>'
                    ],
                ],
                //                    [
                //                        'title'   => 'Мессенджеры',
                //                        'content' => [
                //                            field_render('messengers.telegram', [
                //                                'label' => 'Telegram',
                //                                'value' => $_item['messengers']['telegram'],
                //                            ]),
                //                            field_render('messengers.skype', [
                //                                'label' => 'Skype',
                //                                'value' => $_item['messengers']['skype'],
                //                            ]),
                //                            field_render('messengers.viber', [
                //                                'label' => 'Viber',
                //                                'value' => $_item['messengers']['viber'],
                //                            ]),
                //                            field_render('messengers.whatsapp', [
                //                                'label' => 'Whatsapp',
                //                                'value' => $_item['messengers']['whatsapp'],
                //                            ]),
                //                        ],
                //                    ],
                [
                    'title'   => 'Социальные сети',
                    'content' => [
                        //                            field_render('socials.vk', [
                        //                                'label' => 'VK',
                        //                                'value' => $_item['socials']['vk'],
                        //                            ]),
                        //                            field_render('socials.skype', [
                        //                                'label' => 'Skype',
                        //                                'value' => $_item['socials']['skype'],
                        //                            ]),
                        field_render('socials.instagram', [
                            'label' => 'Instagram',
                            'value' => $_item['socials']['instagram'],
                        ]),
                        field_render('socials.facebook', [
                            'label' => 'Facebook',
                            'value' => $_item['socials']['facebook'],
                        ]),
                        //                            field_render('socials.twitter', [
                        //                                'label' => 'Twitter',
                        //                                'value' => $_item['socials']['twitter'],
                        //                            ]),
                        //                            field_render('socials.google', [
                        //                                'label' => 'Google',
                        //                                'value' => $_item['socials']['google'],
                        //                            ]),
                        //                            field_render('socials.od', [
                        //                                'label' => 'Одноклассники',
                        //                                'value' => $_item['socials']['od'],
                        //                            ]),
                        field_render('socials.telegram', [
                            'label' => 'Telegram',
                            'value' => $_item['socials']['telegram'],
                        ]),
                    ],
                ],
                [
                    'title'   => 'Микроразметка',
                    'content' => [
                        field_render('schema', [
                            'type'       => 'textarea',
                            'value'      => $_item['schema'],
                            'attributes' => [
                                'rows' => 30
                            ],
                            'class'      => 'uk-codeMirror'
                        ]),
                    ],
                ],
            ];
            if (config('os_seo.use.multi_language')) {
                $_form->buttons[] = _l('Добавить перевод', 'oleus.settings.translate', [
                    'p'          => [
                        'setting' => 'contacts',
                        'action'  => 'edit'
                    ],
                    'attributes' => [
                        'class' => 'uk-button uk-button-primary uk-margin-small-right uk-text-uppercase use-ajax'
                    ]
                ]);
            }

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        $_config = $request->except([
            '_token',
            '_method',
            'save',
        ]);
        update_last_modified_timestamp();
        ConfigFileSave::set('os_contacts', array_dot($_config));

        return redirect()
            ->route('oleus.settings', ['page' => 'contacts'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

    public function currency(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Валюта'
        ]);
        $_item = config('os_currencies');
        $_default_locale = config('app.locale');
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'currency';
            $_field_currencies = NULL;
            foreach ($_item['currencies'] as $_key => $_currency) {
                $_field_currencies[] =
                    field_render("currencies.{$_key}.full_name", [
                        'label' => 'Полное название',
                        'type'  => 'markup',
                        'html'  => $_currency['full_name']
                    ]);
                $_field_currencies[] =
                    field_render("currencies.{$_key}.iso_code", [
                        'label' => 'ISO 4217',
                        'type'  => 'markup',
                        'html'  => $_currency['iso_code']
                    ]);
                $_field_currencies[] = '<div uk-grid class="uk-margin uk-child-width-1-2"><div>';
                $_field_currencies[] =
                    field_render("currencies.{$_key}.markup.{$_default_locale}.prefix", [
                        'label' => 'Текст до цены',
                        'value' => config_data_load($_item, "currencies.{$_key}.markup.*.prefix", $_default_locale),
                    ]);
                $_field_currencies[] = '</div><div>';
                $_field_currencies[] =
                    field_render("currencies.{$_key}.markup.{$_default_locale}.suffix", [
                        'label' => 'Текст после цены',
                        'value' => config_data_load($_item, "currencies.{$_key}.markup.*.suffix", $_default_locale),
                    ]);
                $_field_currencies[] = '</div></div>';
                $_field_currencies[] = field_render("currencies.{$_key}.precision", [
                    'type'   => 'radio',
                    'label'  => 'Количество знаков после запятой',
                    'value'  => $_currency['precision'],
                    'values' => [
                        0 => '0 знаков',
                        1 => '1 знак',
                        2 => '2 знака',
                    ],
                    'class'  => 'uk-select2'
                ]);
                $_field_currencies[] = field_render("currencies.{$_key}.precision_mode", [
                    'type'   => 'radio',
                    'label'  => 'Округление суммы',
                    'value'  => $_currency['precision_mode'],
                    'values' => [
                        0 => 'без округления',
                        1 => 'в меньшую',
                        2 => 'в большую',
                        3 => 'в большую до десяков',
                        4 => 'в большую до сотен'
                    ],
                    'class'  => 'uk-select2',
                    'help'   => '<span class="uk-help-block uk-display-block">При выборе <span class="uk-text-bold">"в большую до десятков"</span> и <span class="uk-text-bold">"в большую до сотен"</span> в расчет берется целое число.</span>'
                ]);
            }
            $_form->tabs = [
                [
                    'title'   => 'Настройки',
                    'content' => $_field_currencies,
                ]
            ];
            if (config('os_seo.use.multi_language')) {
                $_form->buttons[] = _l('Добавить перевод', 'oleus.settings.translate', [
                    'p'          => [
                        'setting' => 'currency',
                        'action'  => 'edit'
                    ],
                    'attributes' => [
                        'class' => 'uk-button uk-button-primary uk-margin-small-right uk-text-uppercase use-ajax'
                    ]
                ]);
            }

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        $_config = $request->only([
            'currencies',
        ]);
        update_last_modified_timestamp();
        ConfigFileSave::set('os_currencies', array_dot($_config));

        return redirect()
            ->route('oleus.settings', ['page' => 'currency'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

    public function services(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Сервисы'
        ]);
        $_item = config('os_services');
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'services';
            $_form->tabs = [
                [
                    'title'   => 'Настройки',
                    'content' => [
                        //                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Нова пошта</span></h3>',
                        //                        field_render('nova_poshta.api_key', [
                        //                            'label' => 'Ключ API',
                        //                            'value' => $_item['nova_poshta']['api_key'] ?? NULL,
                        //                        ]),
                        //                        field_render('nova_poshta.expiration_date', [
                        //                            'label'      => 'Дата завершения действия ключа',
                        //                            'value'      => $_item['nova_poshta']['expiration_date'] ?? NULL,
                        //                            'class'      => 'uk-datepicker',
                        //                            'attributes' => []
                        //                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Google Services</span></h3>',
                        field_render('google.gTag', [
                            'label' => 'Google Tag ключ',
                            'value' => $_item['google']['gTag'],
                        ]),
                        field_render('google.reCaptcha_public', [
                            'label' => 'Google reCaptcha публичный ключ',
                            'value' => $_item['google']['reCaptcha_public'],
                        ]),
                        field_render('google.reCaptcha_secret', [
                            'label' => 'Google reCaptcha секретный ключ',
                            'value' => $_item['google']['reCaptcha_secret'],
                        ]),
                        field_render('google.googleMap', [
                            'label' => 'GoogleMap API ключ',
                            'value' => $_item['google']['googleMap'],
                        ]),
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>facebook pixel</span></h3>',
                        field_render('facebook.pixel', [
                            'label' => 'Pixel API',
                            'value' => $_item['facebook']['pixel'] ?? NULL,
                        ]),
                    ],
                ]
            ];

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        $_config = $request->only([
            //                'nova_poshta',
            'google',
            'facebook',
        ]);
        update_last_modified_timestamp();
        ConfigFileSave::set('os_services', array_dot($_config));

        return redirect()
            ->route('oleus.settings', ['page' => 'services'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

    public function shops(Request $request)
    {
        $_wrap = $this->render([
            'seo.title' => 'Настройки магазина'
        ]);
        $_item = config('os_shop');
        $_selected_delivery = collect($_item['delivery_method'])->filter(function ($value) {
            return $value['use'];
        })->keys();
        $_selected_payment = collect($_item['payment_method'])->filter(function ($value) {
            return $value['use'];
        })->keys();
        if ($request->method() == 'GET') {
            $_form = $this->__form();
            $_form->theme = $this->form_theme;
            $_form->route_tag = 'shops';
            $_form->tabs = [
                [
                    'title'   => 'Настройки',
                    'content' => [
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Доставка</span></h3>',
                        field_render('delivery_method', [
                            'type'     => 'checkbox',
                            'label'    => 'Способы',
                            'selected' => $_selected_delivery->toArray(),
                            'values'   => [
                                'pickup'   => 'forms.fields.checkout.delivery_method_pickup',
                                'delivery' => 'forms.fields.checkout.delivery_method_delivery',
                            ],
                            'class'    => 'uk-select2'
                        ]),
                        '<div uk-grid class="uk-child-width-1-3"><div>',
                        field_render('delivery_pickup_percent', [
                            'label'      => '% за самовывоз',
                            'type'       => 'number',
                            'attributes' => [
                                'min'  => 0,
                                'max'  => 100,
                                'step' => 1
                            ],
                            'value'      => $_item['delivery_pickup_percent'] ?? 0,
                        ]),
                        '</div><div>',
                        field_render('delivery_amount', [
                            'label'      => 'Сумма доставки',
                            'type'       => 'number',
                            'attributes' => [
                                'min'  => 0,
                                'step' => 1
                            ],
                            'value'      => $_item['delivery_amount'] ?? 0,
                        ]),
                        '</div><div>',
                        field_render('delivery_free_amount', [
                            'label'      => 'Сумма после которой доставка бесплатна',
                            'type'       => 'number',
                            'attributes' => [
                                'min'  => 0,
                                'step' => 1
                            ],
                            'value'      => $_item['delivery_free_amount'] ?? 0,
                        ]),
                        '</div></div>',
                        '<h3 class="uk-heading-line uk-text-uppercase"><span>Оплата</span></h3>',
                        field_render('payment_method', [
                            'type'     => 'checkbox',
                            'label'    => 'Способы',
                            'selected' => $_selected_payment->toArray(),
                            'values'   => [
                                'cash'            => 'forms.fields.checkout.payment_method_cash',
                                'by_card_courier' => 'forms.fields.checkout.payment_method_by_card_courier',
                                'card'            => 'forms.fields.checkout.payment_method_card',
                            ],
                            'class'    => 'uk-select2'
                        ])
                    ],
                ]
            ];

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }
        $_config = $request->only([
            'payment_method',
            'delivery_free_amount',
            'delivery_amount',
            'delivery_method',
            'pickup_percent'
        ]);
        $_payment = $_config['payment_method'] ?? [];
        $_delivery = $_config['delivery_method'] ?? [];
        foreach ($_item['payment_method'] as $_key => $_data) {
            $_config['payment_method'][$_key] = $_data;
            $_config['payment_method'][$_key]['use'] = isset($_payment[$_key]) ? 1 : 0;
        }
        foreach ($_item['delivery_method'] as $_key => $_data) {
            $_config['delivery_method'][$_key] = $_data;
            $_config['delivery_method'][$_key]['use'] = isset($_delivery[$_key]) ? 1 : 0;;
        }
        ConfigFileSave::set('os_shop', array_dot($_config));

        return redirect()
            ->route('oleus.settings', ['page' => 'shops'])
            ->with('notice', [
                'message' => $this->notice['save_settings'],
                'status'  => 'success'
            ]);
    }

}
