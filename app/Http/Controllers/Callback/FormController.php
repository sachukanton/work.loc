<?php

namespace App\Http\Controllers\Callback;

use App\Library\BaseController;
use App\Models\Form\Forms;
use App\Models\Form\FormsData;
use App\Models\Structure\Page;
use App\Notifications\FormNotification;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class FormController extends BaseController
{

    use Authorizable;
    use Notifiable;

    public function __construct()
    {
        parent::__construct();
    }

    public function submit_form(Request $request, Forms $form)
    {
        try {
            $commands['result'] = FALSE;
            $_form_index = $request->get('form_index', NULL);
            $_form_type = $request->get('form_type', 'block');
            $_form_step = $request->input('fields.form_step', NULL);
            $_form_action_previous_step = $request->get('previous_step_form', NULL);
            $_form_action_next_step = $request->get('next_step_form', NULL);
            $_form_data = $form->formatted_data($_form_index);
            $_form_validate_rules = NULL;
            $_valid = FALSE;
            $_save_data = NULL;
            if ($_form_step) {
                $_submit_step = $_form_data->steps[$_form_step];
                if (isset($_form_data->validation[$_form_step]) && count($_form_data->validation[$_form_step]) && is_null($_form_action_previous_step)) $_form_validate_rules = $_form_data->validation[$_form_step];
            } else {
                $_form_validate_rules = $_form_data->validation;
            }
            if (isset($_form_validate_rules) && count($_form_validate_rules)) {
                $_validate_rules = [
                  //  'captcha' => 'required|reCaptchaV3'
                ];
                $_validate_field_title = [
                 //   'captcha' => trans('forms.fields.captcha')
                ];
                $_validate_field_id = [];
                $_validate_field_multiple = [];
                $_validate_message = '';
                foreach ($_form_validate_rules as $_field) {
                    $_validate_rules[$_field['name']] = $_field['rule'];
                    $_validate_field_title[$_field['name']] = $_field['title'];
                    $_validate_field_id[$_field['name']] = $_field['id'];
                    if ($_field['multiple']) $_validate_field_multiple[$_field['name']] = $_field['id'];
                }
                $_validator = Validator::make($request->all(), $_validate_rules, [], $_validate_field_title);
                $commands['commands'][] = [
                    'command' => 'removeClass',
                    'options' => [
                        'target' => "#{$_form_data->form_id} *",
                        'data'   => 'uk-form-danger error'
                    ]
                ];
                $commands['rules'] = $_validate_rules;
                if ($_validator->fails()) {
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $_validate_message .= "<div>{$_message[0]}</div>";
                        if (isset($_validate_field_id[$_field])) {
                            $commands['commands'][] = [
                                'command' => 'addClass',
                                'options' => [
                                    'target' => "#{$_validate_field_id[$_field]}",
                                    'data'   => 'uk-form-danger error'
                                ]
                            ];
                        } elseif (count($_validate_field_multiple)) {
                            foreach ($_validate_field_multiple as $_field_name => $_field_id) {
                                if (str_is($_field_name, $_field)) {
                                    $commands['commands'][] = [
                                        'command' => 'addClass',
                                        'options' => [
                                            'target' => "#{$_field_id}",
                                            'data'   => 'uk-form-danger error'
                                        ]
                                    ];
                                }
                            }
                        }
                    }
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => $_validate_message,
                            'status' => 'danger'
                        ]
                    ];
                } else {
                    $_valid = TRUE;
                }
            } else {
                $_valid = TRUE;
            }
            if ($_valid && $_form_step) {
                $_current_step = $_form_data->steps[$_form_step];
                $_request_field = $request->only('fields');
                $_session_save_form = Session::get("step_form_{$form->id}");
                if (is_null($_session_save_form)) {
                    $_session_save_form = [
                        'current_step' => $_form_step,
                        'fields'       => []
                    ];
                }
                $_device_template = wrap()->getDeviceTemplate();
                $_template = [
                    "frontend.{$_device_template}.forms.form_steps_item_form_{$form->id}",
                    "frontend.{$_device_template}.forms.form_steps_item",
                    'backend.base.form_steps_item'
                ];
                if ($_form_action_next_step) {
                    $_session_save_form['current_step'] = $_current_step['next'];
                    $_form_data->options_fields->filter(function ($_field) {
                        return $_field['type'] != 'markup' && $_field['type'] != 'break';
                    })->map(function ($_field, $_id) use ($_request_field, $request, &$_session_save_form) {
                        $_request_data = $_request_field['fields'][$_field['field_name']] ?? NULL;
                        switch ($_field['type']) {
                            case 'checkboxes':
                                if ($_request_data) $_request_data = array_keys($_request_data);
                                break;
                            case 'file':
                                if ($request->hasFile("fields.{$_field['field_name']}")) {
                                    $_request_data = [];
                                    try {
                                        $_file = $request->file("fields.{$_field['field_name']}");


                                        if (is_array($_file)) {
                                            foreach ($_file as $_attach_file) {
                                                $_file_name = $_attach_file->getClientOriginalName();
                                                $_file_extension = $_attach_file->getClientOriginalExtension();
                                                $_file_real_path = $_attach_file->getRealPath();
                                                $_attach_file_name = str_slug(basename($_file_name, ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                                                Storage::disk('local')
                                                    ->put("tmp/{$_attach_file_name}", file_get_contents($_file_real_path));
                                                $_request_data[] = [
                                                    'name'     => $_file_name,
                                                    'tmp_name' => $_attach_file_name
                                                ];
                                            }
                                        } else {
                                            $_file_name = $_file->getClientOriginalName();
                                            $_file_extension = $_file->getClientOriginalExtension();
                                            $_file_real_path = $_file->getRealPath();
                                            $_attach_file_name = str_slug(basename($_file_name, ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                                            Storage::disk('local')
                                                ->put("tmp/{$_attach_file_name}", file_get_contents($_file_real_path));
                                            $_request_data[] = [
                                                'name'     => $_file_name,
                                                'tmp_name' => $_attach_file_name
                                            ];
                                        }
                                    } catch (\Exception $exception) {
                                    }
                                }
                                break;
                        }
                        if (isset($_request_field['fields'][$_field['field_name']])) $_session_save_form['fields'][$_id] = $_request_data;
                    });
                    Session::put("step_form_{$form->id}", $_session_save_form);
                    $_step_number = array_search($_current_step['next'], $_form_data->steps_sort);
                    if ($_form_type == 'block') {
                        $commands['commands'][] = [
                            'command' => 'redirect',
                            'options' => [
                                'url' => "/forms/{$form->id}/step-{$_step_number}"
                            ]
                        ];
                    } else {
                        $_form_step = View::first($_template, [
                            '_step_item' => $_form_data->steps[$_current_step['next']],
                            '_form'      => $form,
                            '_form_data' => $_form_data
                        ])->render(function ($view, $_content) {
                            return clear_html($_content);
                        });
                        $commands['commands'][] = [
                            'command' => 'replaceWith',
                            'options' => [
                                'target' => "#{$_form_data->form_id}-steps-box",
                                'data'   => $_form_step,
                            ]
                        ];
                        $commands['commands'][] = [
                            'command' => 'changeUrl',
                            'options' => [
                                'url' => $_step_number > 1 ? "/forms/{$form->id}/step-{$_step_number}" : "/forms/{$form->id}"
                            ]
                        ];
                    }
                } elseif ($_form_action_previous_step) {
                    $_session_save_form['current_step'] = $_current_step['prev'];
                    Session::put("step_form_{$form->id}", $_session_save_form);
                    $_step_number = array_search($_current_step['next'], $_form_data->steps_sort);
                    if ($_form_type == 'page') {
                        $commands['commands'][] = [
                            'command' => 'changeUrl',
                            'options' => [
                                'url' => $_step_number > 1 ? "/forms/{$form->id}/step-{$_step_number}" : "/forms/{$form->id}"
                            ]
                        ];
                    }
                    $_form_step = View::first($_template, [
                        '_step_item' => $_form_data->steps[$_current_step['prev']],
                        '_form'      => $form,
                        '_form_data' => $_form_data
                    ])->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
                    $commands['commands'][] = [
                        'command' => 'replaceWith',
                        'options' => [
                            'target' => "#{$_form_data->form_id}-steps-box",
                            'data'   => $_form_step,
                        ]
                    ];
                } else {
                    $_form_data->options_fields->filter(function ($_field) {
                        return $_field['type'] != 'markup' && $_field['type'] != 'break';
                    })->map(function ($_field, $_id) use ($_request_field, $request, &$_session_save_form) {
                        $_request_data = $_request_field['fields'][$_field['field_name']] ?? NULL;
                        switch ($_field['type']) {
                            case 'checkboxes':
                                if ($_request_data) $_request_data = array_keys($_request_data);
                                break;
                            case 'file':
                                if ($request->hasFile("fields.{$_field['field_name']}")) {
                                    $_request_data = [];
                                    try {
                                        $_file = $request->file("fields.{$_field['field_name']}");
                                        if (is_array($_file)) {
                                            foreach ($_file as $_attach_file) {
                                                $_request_data[] = [
                                                    'name'      => $_attach_file->getClientOriginalName(),
                                                    'extension' => $_attach_file->getClientOriginalExtension(),
                                                    'realPath'  => $_attach_file->getRealPath()
                                                ];
                                            }
                                        } else {
                                            $_request_data[] = [
                                                'name'      => $_file->getClientOriginalName(),
                                                'extension' => $_file->getClientOriginalExtension(),
                                                'realPath'  => $_file->getRealPath()
                                            ];
                                        }
                                    } catch (\Exception $exception) {
                                    }
                                }
                                break;
                        }
                        if (isset($_request_field['fields'][$_field['field_name']])) $_session_save_form['fields'][$_id] = $_request_data;
                    });
                    $_save_data = $_form_data->options_fields
                        ->filter(function ($_field) {
                            return $_field['type'] != 'markup' && $_field['type'] != 'break';
                        })
                        ->map(function ($_field, $_id) use ($request, $_session_save_form) {
                            $_request_data = $_session_save_form['fields'][$_id] ?? NULL;
                            if ($_request_data) {
                                switch ($_field['type']) {
                                    case 'checkboxes':
                                    case 'radios':
                                    case 'select':
                                        $_tmp_data = [];
                                        if (is_array($_request_data)) {
                                            foreach ($_request_data as $_option_value) if (isset($_field['values'][$_option_value])) $_tmp_data[] = $_field['values'][$_option_value];
                                            $_request_data = implode(', ', $_tmp_data);
                                        } else {
                                            if (isset($_field['values'][$_request_data])) $_request_data = $_field['values'][$_request_data];
                                        }
                                        break;
                                    case 'file':
                                        $_tmp_data = NULL;
                                        try {
                                            $_base_url = config('app.url');
                                            foreach ($_request_data as $_attach_file) {
                                                if (!Storage::disk('local')->exists("public/{$_attach_file['tmp_name']}")) {
                                                    Storage::disk('local')->copy("tmp/{$_attach_file['tmp_name']}", "public/{$_attach_file['tmp_name']}");
                                                }
                                                $_tmp_data[] = "<a href=\"{$_base_url}/storage/{$_attach_file['tmp_name']}\" target=\"_blank\">{$_attach_file['tmp_name']}</a>";
                                            }
                                            if (count($_tmp_data)) $_request_data = implode(', ', $_tmp_data);
                                        } catch (\Exception $exception) {
                                        }
                                        break;
                                }
                            }

                            return [
                                'label' => $_field['field_label'],
                                'data'  => $_request_data
                            ];
                        });
                    Session::forget("step_form_{$form->id}");
                    $_form_step = View::first($_template, [
                        '_step_item' => $_form_data->steps[$_form_data->first_step],
                        '_form'      => $form,
                        '_form_data' => $_form_data
                    ])->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
                    $commands['commands'][] = [
                        'command' => 'replaceWith',
                        'options' => [
                            'target' => "#{$_form_data->form_id}-steps-box",
                            'data'   => $_form_step,
                        ]
                    ];
                }
            } elseif ($_valid) {
                $_request_field = $request->only('fields');
                $_save_data = $_form_data->options_fields
                    ->filter(function ($_field) {
                        return $_field['type'] != 'markup' && $_field['type'] != 'break';
                    })
                    ->map(function ($_field, $_id) use ($_request_field, $request) {
                        $_request_data = $_request_field['fields'][$_field['field_name']] ?? NULL;
                        if ($_request_data) {
                            switch ($_field['type']) {
                                case 'checkboxes':
                                case 'radios':
                                case 'select':
                                    $_tmp_data = [];
                                    if (is_array($_request_data)) {
                                        foreach ($_request_data as $_option_value) if (isset($_field['values'][$_option_value])) $_tmp_data[] = $_field['values'][$_option_value];
                                        $_request_data = implode(', ', $_tmp_data);
                                    } else {
                                        if (isset($_field['values'][$_request_data])) $_request_data = $_field['values'][$_request_data];
                                    }
                                    break;
                                case 'file':
                                    $_tmp_data = NULL;
                                    try {
                                        $_file = $request->file("fields.{$_field['field_name']}");
                                        $_base_url = config('app.url');
                                        if (is_array($_file)) {
                                            foreach ($_file as $_attach_file) {
                                                $_file_extension = $_attach_file->getClientOriginalExtension();
                                                $_attach_file_name = str_slug(basename($_attach_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                                                Storage::disk('base')
                                                    ->put("form_attach/{$_attach_file_name}", file_get_contents($_attach_file->getRealPath()));
                                                $_tmp_data[] = "<a href=\"{$_base_url}/form_attach/{$_attach_file_name}\" target=\"_blank\">{$_attach_file_name}</a>";
                                            }
                                        } else {
                                            $_file_extension = $_file->getClientOriginalExtension();
                                            $_attach_file_name = str_slug(basename($_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                                            Storage::disk('base')
                                                ->put("form_attach/{$_attach_file_name}", file_get_contents($_file->getRealPath()));
                                            $_tmp_data[] = "<a href=\"{$_base_url}/form_attach/{$_attach_file_name}\" target=\"_blank\">{$_attach_file_name}</a>";
                                        }
                                        if (count($_tmp_data)) $_request_data = implode(', ', $_tmp_data);
                                    } catch (\Exception $exception) {
                                    }
                                    break;
                            }
                        }

                        return [
                            'label' => $_field['field_label'],
                            'data'  => $_request_data
                        ];
                    });
            }
            if ($_save_data) {
                $commands['result'] = TRUE;
                $_save = [
                    'user_id'      => Auth::check() ? Auth::user()->id : NULL,
                    'form_id'      => $form->id,
                    'data'         => json_encode($_save_data),
                    'referer_path' => $request->headers->get('referer')
                ];
                $_item = new FormsData();
                $_item->fill($_save);
                $_item->save();
                try {
                    if ($_emails = $_item->_form->email_to_receive) {
                        $_emails = explode(',', $_emails);
                        foreach ($_emails as &$_email) $_email = trim($_email);
                        Notification::route('mail', $_emails)
                            ->notify(new FormNotification($_item));
                        $_item->update([
                            'notified' => 1
                        ]);
                    }
                } catch (\Exception $exception) {
                }
                try {
                    $token = "2003266509:AAFoe5DHkQZpxyKQ_HNwSDHniJztEiP7OV0";
                    $chat_id = "-1001573651332";
                    $fields = $request->get('fields');
                    $name = $fields['field_3'];
                    $phone = $fields['field_9'];
                    $txt = "<b>".$name."</b> ".$phone;
                    fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");
                } catch (\Exception $exception) {
                }
                $_settings_form = $form->settings;
                if ($_settings_form && isset($_settings_form->send->target->use) && $_settings_form->send->target->use) {
                    $_target = $_settings_form->send->target;
                    $_ga = [];
                    if ($_target->category) $_ga['category'] = $_target->category;
                    if ($_target->event) $_ga['event'] = $_target->event;
                    if ($_target->action) $_ga['event_action'] = $_target->action;
                    if (count($_ga)) {
                        $commands['commands'][] = [
                            'command' => 'analyticsGtag',
                            'options' => $_ga
                        ];
                    }
                    if ($_target->fbq_event) {
                        $commands['commands'][] = [
                            'command' => 'analyticsFbq',
                            'options' => [
                                'event' => $_target->fbq_event
                            ]
                        ];
                    }
                }
                if ($form->completion_type == 1 && $form->completion_page_id) {
                    $_thanks_page = Page::find($form->completion_page_id);
                    $commands['commands'][] = [
                        'command' => 'redirect',
                        'options' => [
                            'url' => _u($_thanks_page->generate_url)
                        ]
                    ];
                } elseif ($form->completion_type == 2 && $form->completion_modal_text) {
                    $commands['commands'][] = [
                        'command' => 'UK_modal',
                        'options' => [
                            'content'     => View::first([
                                "frontend.{$this->deviceTemplate}.partials.modal",
                                'backend.partials.modal'
                            ], [
                                'message' => $form->completion_modal_text
                            ])->render(function ($view, $_content) {
                                return clear_html($_content);
                            }),
                            'id'          => 'message-ajax-modal',
                            'classDialog' => 'uk-margin-auto-vertical uk-width-auto',
                            'classModal'  => 'uk-flex uk-flex-top'
                        ]
                    ];
                } else {
                    $commands['commands'][] = [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => 'Form data submitted.',
                            'status' => 'success'
                        ]
                    ];
                }
                spy("На сайте заполнена форма \"<span class='uk-text-bold'>{$form->title}</span>\". <a href='/oleus/forms-data/{$_item->id}/edit'>Просмотреть данные отправки</a>.", 'success');
                $commands['commands'][] = [
                    'command' => 'clearForm',
                    'options' => [
                        'target' => "#{$_form_data->form_id}",
                    ]
                ];
            }
        } catch (\Exception $exception) {
            $commands['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                ]
            ];
        }

        return response($commands, 200);
    }

    public function open_form(Request $request, Forms $form)
    {
        try {
            $_options = $request->only([
                'index',
                'view'
            ]);
            $_form_render = $form->_render($_options);
            $_settings_form = $form->settings;
            if ($_settings_form && isset($_settings_form->open_form->target->use) && $_settings_form->open_form->target->use) {
                $_target = $_settings_form->open_form->target;
                $_ga = NULL;
                if ($_target->category) $_ga['category'] = $_target->category;
                if ($_target->event) $_ga['event'] = $_target->event;
                if ($_target->action) $_ga['event_action'] = $_target->action;
                if (count($_ga)) {
                    $commands['commands'][] = [
                        'command' => 'analyticsGtag',
                        'options' => $_ga
                    ];
                }
                if ($_target->fbq_event) {
                    $commands['commands'][] = [
                        'command' => 'analyticsFbq',
                        'options' => [
                            'event' => $_target->fbq_event
                        ]
                    ];
                }
            }
            $commands['commands'][] = [
                'command' => 'UK_modal',
                'options' => [
                    'content'     => clear_html("<div class=\"uk-modal-body\"><button class=\"uk-modal-close-outside\" type=\"button\" uk-close></button><div>{$_form_render}</div></div>"),
                    'id'          => 'constructor-form-ajax-modal',
                    'classDialog' => 'uk-margin-auto-vertical',
                    'classModal'  => 'uk-flex uk-flex-top'
                ]
            ];
        } catch (\Exception $exception) {
            $commands['commands'][] = [
                'command' => 'UK_notification',
                'options' => [
                    'text'   => trans('notifications.an_error_has_occurred'),
                    'status' => 'danger',
                ]
            ];
        }

        return response($commands, 200);
    }

    public function render_form(Request $request, Forms $form, $step = NULL)
    {
        wrap()->set('page.title', $form->title);
        wrap()->set('seo.title', $form->title);
        wrap()->set('page.breadcrumb', breadcrumb_render(['entity' => $form]));
        if ($step) {
            $pattern = '/step-[0-9]+/';
            preg_match($pattern, $step, $_step);
            $step = count($_step) ? (int)str_replace('step-', '', array_shift($_step)) : 1;
        } else {
            $step = 1;
        }
        $form->render_type = 'page';
        $_item = $form;
        $_render = $form->_render([
            'step' => $step
        ]);
        $_template = [
            "frontend.{$this->deviceTemplate}.pages.page_form_{$form->id}",
            "frontend.{$this->deviceTemplate}.pages.page_form",
            'backend.base.page_form'
        ];
        $_response = View::first($_template, compact('_item', '_render'));
        $_wrap = wrap()->render([
            'step' => $step
        ]);
        if ($_response) {
            if (is_a($_response, 'Illuminate\View\View')) {
                $_response->with(compact('_wrap'));
            } elseif ($request->ajax()) {
                $_response = response($_response, 200);
            }

            return $_response;
        }
        abort(404);
    }

}
