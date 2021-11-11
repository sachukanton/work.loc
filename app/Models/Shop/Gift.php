<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class Gift extends BaseModel
{
    protected $table = 'shop_gifts';
    protected $guarded = [];
    public $translatable = [
        'title'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Relationships
     */
    public function _product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->withDefault();
    }

    public static function getInfo()
    {
        $_response = NULL;
        $_basket = app('basket');
        if ($_basket) {
            $_total_amount = $_basket->total_amount;
            $_config = config('os_shop');
            if ($_basket->delivery == 'pickup') {
                $_percent = $_config['delivery_pickup_percent'] ?? 0;
                $_total_amount = $_total_amount - ceil($_total_amount * ($_percent / 100));
            }
            $_gifts = self::where('status', 1)
                ->orderBy('amount')
                ->orderByDesc('sort')
                ->get();
            if ($_gifts->isNotEmpty()) {
                $_response = [
                    'steps'          => collect([]),
                    'current_amount' => 0,
                    'not_enough'     => 0,
                    'current_step'   => NULL,
                    'next_step'      => NULL,
                    'max_step'       => NULL,
                    'view_text'      => NULL,
                    'amount'         => $_total_amount,
                    'ratio'          => 0
                ];
                $_step = 0;
                $_gifts->map(function ($g) use (&$_response, &$_step) {
                    $_checked = FALSE;
                    $_diff = 0;
                    if ($g->amount > $_response['amount']) {
                        $_diff = $g->amount - $_response['amount'];
                    } else {
                        $_checked = TRUE;
                        $_response['current_step'] = $_step;
                        $_response['current_amount'] = $g->amount;
                    }
                    $_response['steps']->put($_step, [
                        'id'          => $g->id,
                        'type'        => $g->type,
                        'amount'      => $g->amount,
                        'diff_amount' => $_diff,
                        'title'       => $g->title,
                        'image_url'   => $g->_preview_asset('shop_gifts', [
                            'only_way' => TRUE
                        ]),
                        'checked'     => $_checked
                    ]);
                    $_response['max_step'] = $_step;
                    $_step++;
                });
                if ($_response['max_step']) {
                    $_max_step = $_response['steps']->get($_response['max_step']);
                    $_response['ratio'] = round($_response['amount'] * 100 / $_max_step['amount'], 0);
                    if ($_response['ratio'] > 98) $_response['ratio'] = 100;
                }
                if (is_null($_response['current_step'])) {
                    $_response['next_step'] = 0;
                } elseif ($_response['current_step'] < $_response['max_step']) {
                    $_response['next_step'] = $_response['current_step'] + 1;
                }
                if (!is_null($_response['next_step'])) {
                    $_next_step = $_response['steps']->get($_response['next_step']);
                    $_response['not_enough'] = view_price($_next_step['diff_amount'], $_next_step['diff_amount']);
                    $_response['view_text'] = trans('shop.checkout.gifts', ['amount' => $_response['not_enough']['format']['view_price']]);
                }
            }
        }

        return $_response;
    }

    public static function getGift()
    {
        $_gifts = self::getInfo();
        if ($_gifts && is_numeric($_gifts['current_step'])) {
            return $_gifts['steps']->has($_gifts['current_step']) ? $_gifts['steps']->get($_gifts['current_step']) : NULL;
        }

        return NULL;
    }
}
