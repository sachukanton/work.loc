<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\View;

    class Block extends BaseModel
    {

        protected $table = 'blocks';
        protected $guarded = [];
        public $render_index;
        public $translatable = [
            'title',
            'sub_title',
            'body'
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _load($options = [])
        {
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->body = content_render($this);
            $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
            if (isset($_options['index']) && $_options['index']) $this->render_index = $_options['index'];
            if ($this->render_index && $this->style_id) $this->style_id .= "-{$this->render_index}";

            return $this;
        }

        public function _render($options = NULL)
        {
            if ($this->invisible) return NULL;
            $this->_load($options);
            $_template = [
                "frontend.{$this->deviceTemplate}.blocks.block_{$this->id}",
                "frontend.{$this->deviceTemplate}.blocks.block",
                "frontend.default.blocks.block_{$this->id}",
                "frontend.default.blocks.block",
                'backend.base.block'
            ];
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
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->_load($_options);
            $_item = $this;
            $_template = [];
            if (isset($_options['view']) && $_options['view']){
                $_template = [
                    "frontend.{$this->deviceTemplate}.{$_options['view']}",
                    "frontend.default.{$_options['view']}",
                ];
            }
            $_template = array_merge($_template, [
                "frontend.{$this->deviceTemplate}.blocks.shortcut_block_{$this->id}",
                "frontend.{$this->deviceTemplate}.blocks.shortcut_block",
                "frontend.default.blocks.shortcut_block_{$this->id}",
                'frontend.default.blocks.shortcut_block',
                'backend.base.block'
            ]);

            return View::first($_template, compact('_item'))
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }

    }
