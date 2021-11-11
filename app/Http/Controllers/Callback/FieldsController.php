<?php

    namespace App\Http\Controllers\Callback;

    use App\Library\BaseController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\View;

    class FieldsController extends BaseController
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function field(Request $request, $type, $action = NULL)
        {
            $commands = NULL;
            switch ($type) {
                case 'table':
                    $commands['commands'][] = [
                        'command' => 'append',
                        'options' => [
                            'target' => '#field-table-items',
                            'data'   => View::make('backend.fields.table_item', [
                                'name' => $request->get('name'),
                                'cols' => $request->get('cols')
                            ])->render(function ($view, $_content) {
                                return clear_html($_content);
                            }),
                        ]
                    ];
                    break;
            }
            if (is_null($commands)) {
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
    }
