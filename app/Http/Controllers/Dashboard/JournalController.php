<?php

namespace App\Http\Controllers\Dashboard;

use App\Library\BaseController;
use App\Models\Components\Journal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->titles = [
            'index'     => 'Журнал событий',
            'create'    => '',
            'edit'      => '',
            'translate' => '',
            'delete'    => '',
        ];
        $this->middleware([
            'permission:journal_read'
        ]);
        $this->base_route = 'journal';
        $this->permissions = [
            'read' => 'journal_read',
        ];
        $this->entity = new Journal();
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
        $_query = Journal::from('journal as j')
            ->when($_filter, function ($query) use ($_filter) {
                if ($_filter['create_from']) $query->where('created_at', '>=', Carbon::parse($_filter['create_from'])->format('Y-m-d 00:00:00'));
                if ($_filter['create_to']) $query->where('created_at', '<=', Carbon::parse($_filter['create_to'])->format('Y-m-d 23:59:59'));
            })
            ->distinct()
            ->select([
                'j.*',
            ])
            ->orderByDesc('j.id')
            ->paginate($this->entity->getPerPage(), ['j.id']);
        $_buttons = [];
        $_headers = [
            [
                'class' => 'uk-width-auto uk-text-nowrap',
                'data'  => 'Дата и время',
            ],
            [
                'class' => 'uk-width-expand',
                'data'  => 'Событие',
            ],
        ];
        if ($_query->isNotEmpty()) {
            $_items = $_query->map(function ($_item) use ($_user) {
                $_item->class = 'primary';
                if ($_item->type == 'warning') $_item->class = 'warning';
                if ($_item->type == 'error') $_item->class = 'danger';
                if ($_item->type == 'success') $_item->class = 'success';
                $_response = [
                    'class' => "uk-alert-{$_item->class}",
                    'data'  => [
                        $_item->created_at->format('Y-m-d H:i:s'),
                        $_item->message
                    ]
                ];

                return $_response;
            });
        }
        $_filters[] = [
            'data' => field_render('create_from', [
                'value'      => $_filter['create_from'] ?? NULL,
                'attributes' => [
                    'placeholder' => 'Дата с'
                ],
                'class'      => 'uk-datepicker',
            ])
        ];
        $_filters[] = [
            'data' => field_render('create_to', [
                'value'      => $_filter['create_to'] ?? NULL,
                'attributes' => [
                    'placeholder' => 'Дата по'
                ],
                'class'      => 'uk-datepicker',
            ])
        ];
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

}
