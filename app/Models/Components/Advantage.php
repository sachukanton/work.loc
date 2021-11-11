<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\View;

    class Advantage extends BaseModel
    {

        protected $table = 'advantages';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'title',
            'sub_title',
            'body'
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _items()
        {
            return $this->hasMany(AdvantageItems::class, 'advantage_id')
                ->active()
                ->orderBy('sort');
        }

        public function _load($options = [])
        {
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->body = content_render($this);
            if (isset($_options['index']) && $_options['index']) $this->render_index = $_options['index'];
            if ($this->render_index && $this->style_id) $this->style_id .= "-{$this->render_index}";

            return $this;
        }

        public function _render($options)
        {
            if ($this->invisible) return NULL;
            $this->_load($options);
            $_template = [
                "frontend.default.blocks.advantage_{$this->id}",
                "frontend.default.blocks.advantage_block",
                'backend.base.advantage_block'
            ];
            if ($options['view']) array_unshift($_template, $options['view']);
            $this->_items = $this->_items()
                ->remember(REMEMBER_LIFETIME)
                ->get();
            if ($this->_items->isNotEmpty()) {
                $_item = $this;

                return View::first($_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
        }

        public function getShortcut($options = [])
        {
            if (!is_bool($this->view_access)) return NULL;
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->_load($_options);
            $this->_items = $this->_items()
                ->remember(REMEMBER_LIFETIME)
                ->get();
            if ($this->_items->isNotEmpty()) {
                $_item = $this;
                $_template = [];
                if (isset($_options['view']) && $_options['view']) {
                    $_template = [
                        "frontend.{$this->deviceTemplate}.{$_options['view']}",
                        "frontend.default.{$_options['view']}",
                    ];
                }
                $_template = array_merge($_template, [
                    "frontend.{$this->deviceTemplate}.advantages.shortcut_advantage_{$this->id}",
                    "frontend.{$this->deviceTemplate}.advantages.shortcut_advantage",
                    "frontend.default.advantages.shortcut_advantage_{$this->id}",
                    'frontend.default.advantages.shortcut_advantage',
                    'backend.base.advantage_block'
                ]);
                return View::first($_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
        }

    }
