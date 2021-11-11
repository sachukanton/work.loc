<?php

    namespace App\Models\Structure;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\View;

    class Faq extends BaseModel
    {

        protected $table = 'faqs';
        protected $guarded = [];
        public $translatable = [
            'question',
            'answer'
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _render_block($options)
        {
            $_items = self::active()
                ->where('visible_on_block', 1)
                ->orderBy('sort')
                ->orderBy('question')
                ->remember(REMEMBER_LIFETIME)
                ->get();
            if ($_items->isNotEmpty()) {
                $_template = [
                    "frontend.{$this->deviceTemplate}.blocks.faq_block",
                    'frontend.default.blocks.faq_block',
                    'backend.base.faq_block'
                ];
                if ($options['view']) array_unshift($_template, $options['view']);

                return View::first($_template, compact('_items', '_locale'))
                    ->render(function ($view, $_content){
                        return clear_html($_content);
                    });
            }

            return NULL;
        }

        public function getSchemaAttribute()
        {
            $_response = [
                "@context"   => "https://schema.org",
                "@type"      => "FAQPage",
                "mainEntity" => [],
            ];

            return json_encode($_response);
        }

    }
