<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\View;

    class Slider extends BaseModel
    {

        protected $table = 'sliders';
        protected $guarded = [];
        public $render_index;
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _items()
        {
            return $this->hasMany(SliderItems::class, 'slider_id')
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

        public function _render($options = NULL)
        {
            if ($this->invisible) return NULL;
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->_load($_options);
            $this->_items = $this->_items()
                ->active()
                ->get();
            if ($this->_items->isNotEmpty()) {
                $_item = $this;
                $_template = [];
                if (isset($_options['view']) && $_options['view']){
                    $_template = [
                        "frontend.{$this->deviceTemplate}.{$_options['view']}",
                        "frontend.default.{$_options['view']}",
                    ];
                }
                $_template = array_merge($_template, [
                    "frontend.{$this->deviceTemplate}.sliders.slider_{$this->id}",
                    "frontend.{$this->deviceTemplate}.sliders.slider",
                    'backend.base.slider'
                ]);
                return View::first($_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
        }

        public function _short_code($options = [])
        {
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            $this->_load($options);
            $_template = [
                "frontend.{$this->device_template}.sliders.short_code_slider_{$this->id}",
                "frontend.{$this->device_template}.sliders.short_code_slider",
                'backend.base.slider'
            ];
            if (isset($options['view']) && $options['view']) array_unshift($_template, "frontend.{$this->device_template}.{$options['view']}");
            if ($this->_items->isNotEmpty()) {
                if (!is_bool($this->view_access)) $this->invisible = TRUE;
                $_item = $this;
                $_view = NULL;
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access) && $_item->view_access) {
                        $_view = View::first($_template, compact('_item'))
                            ->render();
                    } elseif (is_string($_item->view_access)) {
                        $_view = View::first($_template, compact('_item'))
                            ->render();
                        $_item->invisible = TRUE;
                        $_view = $this->invisible_prefix . $_view . $this->invisible_suffix;
                    }
                }

                return $_view;
            }
        }

    }
