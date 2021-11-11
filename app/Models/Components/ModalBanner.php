<?php

namespace App\Models\Components;

use App\Library\BaseModel;
use App\Models\File\File;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class ModalBanner extends BaseModel
{

    protected $table = 'modal_banners';
    protected $guarded = [];
    public $timestamps = FALSE;
    public $translatable = [
        'link',
        'background_pc',
        'background_mob'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _background_pc()
    {
        return $this->hasOne(File::class, 'id', 'background_pc');
    }

    public function _background_mobile()
    {
        return $this->hasOne(File::class, 'id', 'background_mob');
    }

    public function _render($options = [])
    {
        if ($this->invisible) return NULL;
        $_options = array_merge([
            'view'  => NULL,
            'index' => NULL,
        ], $options);
        if (isset($_options['index']) && $_options['index']) $this->render_index = $_options['index'];
        if ($this->render_index && $this->style_id) $this->style_id .= "-{$this->render_index}";
        $_template = [
            "frontend.{$this->deviceTemplate}.partials.modal_banners",
            "frontend.default.partials.modal_banners",
        ];
        if (isset($_options['view']) && $_options['view']) array_unshift($_template, "frontend.{$this->deviceTemplate}.{$_options['view']}");
        $_item = $this;

        return View::first($_template, compact('_item'))
            ->render(function ($view, $_content) {
                return clear_html($_content);
            });
    }

    public static function getFirst()
    {
        global $wrap;
        $_cookie = $_COOKIE['modalBanner'] ?? 0;
        $_item = self::active()
            ->first();
        if ($_item && $_cookie == 0) {
            if ($wrap['device']['type'] == 'pc') {
                $_background = image_render($_item->_background_pc, NULL, [
                    'only_way' => TRUE
                ]);
            } else {
                $_background = image_render($_item->_background_pc, NULL, [
                    'only_way' => TRUE
                ]);
            }
            $_banner = '<div id="modal-banner" class="uk-flex-top uk-modal"><div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" style="padding:0!important;background:transparent!important;"><button class="uk-modal-close-default" type="button" uk-close style="top:-25px;right:-5px;"></button>';
            if ($_item->link) {
                $_banner .= '<a href="' . $_item->link . '" ' . ($_item->link_attributes ? : NULL) . ' class="uk-display-block">';
            }
            $_banner .= '<img src="' . $_background . '" alt="" class="uk-display-block uk-width-1-1">';
            if ($_item->link) {
                $_banner .= '</a>';
            }
            $_banner .= '</div></div>';

            return $_banner;
        }

        return NULL;
    }
}
