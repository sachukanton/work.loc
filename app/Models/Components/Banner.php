<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\View;

    class Banner extends BaseModel
    {

        protected $table = 'banners';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'link',
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public static function _banners()
        {
            return self::with([
                '_background'
            ])
                ->active()
                ->orderBy('position')
                ->orderBy('sort')
                ->get()
                ->groupBy('position');
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
                "frontend.{$this->deviceTemplate}.banners.banner_{$this->id}",
                "frontend.{$this->deviceTemplate}.banners.banner",
                "frontend.default.banners.banner_{$this->id}",
                "frontend.default.banners.banner",
                'backend.base.banner'
            ];
            if (isset($_options['view']) && $_options['view']) array_unshift($_template, "frontend.{$this->deviceTemplate}.{$_options['view']}");
            $_item = $this;

            return View::first($_template, compact('_item'))
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }

        public function getShortcut($options = [])
        {
            if (!is_bool($this->view_access)) return NULL;
            $_options = array_merge([
                'type'  => 'banner',
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            if ($_options['type'] == 'banner') {
                $_item = $this;
                $_template = [];
                if (isset($_options['index']) && $_options['index']) $this->render_index = $_options['index'];
                if ($this->style_id && $this->render_index) $this->style_id .= "-{$this->render_index}";
                if (isset($_options['view']) && $_options['view']) {
                    $_template = [
                        "frontend.{$this->deviceTemplate}.{$_options['view']}",
                        "frontend.default.{$_options['view']}",
                    ];
                }
                $_template = array_merge($_template, [
                    "frontend.{$this->deviceTemplate}.banners.shortcut_banner_{$this->id}",
                    "frontend.{$this->deviceTemplate}.banners.shortcut_banner",
                    "frontend.default.banners.shortcut_banner_{$this->id}",
                    'frontend.default.banners.shortcut_banner',
                    'backend.base.banner'
                ]);

                return View::first($_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }

            return NULL;
        }

    }
