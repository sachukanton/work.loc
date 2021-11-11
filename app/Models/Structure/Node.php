<?php

namespace App\Models\Structure;

use App\Library\BaseModel;
use App\Models\TmpMetaTags;
use Illuminate\Support\Facades\View;

class Node extends BaseModel
{

    protected $table = 'nodes';
    protected $guarded = [];
    protected $dates = [
        'published_at'
    ];
    private $viewMode;
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'teaser',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _page()
    {
        return $this->belongsTo(Page::class, 'page_id')
            ->with([
                '_tmp_meta_tags'
            ]);
    }

    public function scopeNodeTypes()
    {
        $_pages = Page::where('type', 'list_nodes')
            ->orderBy('title')
            ->orderBy('sort')
            ->get();

        return $_pages;
    }

    public function getSchemaAttribute()
    {
        global $wrap;
        $_response = [
            "@context"         => "https://schema.org",
            "@type"            => "Article",
            "mainEntityOfPage" => [
                "@type" => "WebPage",
                "@id"   => "{$wrap['seo']['base_url']}{$this->generate_url}"
            ],
            "headline"         => $this->title,
            "image"            => $this->preview_fid ? $wrap['seo']['base_url'] . $this->_preview_asset('nodeTeaser_300_150', [
                    'only_way'       => TRUE,
                    'no_last_modify' => TRUE
                ]) : NULL,
            "datePublished"    => $this->published_at->format('Y-m-d') ? : $this->created_at->format('Y-m-d'),
            "dateModified"     => $this->updated_at->format('Y-m-d'),
            "author"           => [
                "@type" => "Organization",
                "name"  => $wrap['page']['site_name']
            ],
            "publisher"        => [
                "@type" => "Organization",
                "name"  => $wrap['page']['site_name'],
                "logo"  => [
                    "@type"  => "ImageObject",
                    "url"    => "{$wrap['seo']['base_url']}/template/logotypes/logotype.png",
                    "width"  => NULL,
                    "height" => NULL
                ]
            ],
        ];

        return json_encode($_response);
    }

    public function _load($view_mode = 'full')
    {
        switch ($view_mode) {
            case 'teaser':
//                $this->teaser = content_render($this);
                break;
            default:
                $this->body = content_render($this);
                $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
                $this->relatedFiles = $this->_files_related()->wherePivot('type', 'files')->get();
                break;
        }
    }

    public function _render($options = NULL)
    {
        global $wrap;
        $_view = $options['view_mode'] ?? NULL;
        $_page_entity = $this->_page;
        $_page_class = NULL;
        if ($_page_entity->style_class) $_page_class[] = $_page_entity->style_class;
        if ($this->style_class) $_page_class[] = $this->style_class;
        $_page_class = [
            $_page_entity->style_class,
            $this->style_class
        ];
        $this->_load($_view);
        $_page_entity_tmp_meta = [
            'title'       => short_code($_page_entity->_tmp_meta_tags->meta_title, $this),
            'description' => short_code($_page_entity->_tmp_meta_tags->meta_description, $this),
            'keywords'    => short_code($_page_entity->_tmp_meta_tags->meta_keywords, $this),
        ];
        $_set_wrap = [
            'seo.title'         => $this->meta_title ? $this->meta_title : ($_page_entity_tmp_meta['title'] ? : $this->title),
            'seo.keywords'      => $this->meta_keywords ? $this->meta_keywords : $_page_entity_tmp_meta['keywords'],
            'seo.description'   => $this->meta_description ? $this->meta_description : $_page_entity_tmp_meta['description'],
            'seo.robots'        => $this->meta_robots,
            'seo.last_modified' => $this->last_modified,
            'page.title'        => $this->title,
            'page.style_id'     => $this->style_id,
            'page.style_class'  => $_page_class,
            'page.breadcrumb'   => breadcrumb_render([
                'entity' => $this,
                'parent' => $_page_entity
            ]),
            'seo.open_graph'    => [
                'title'       => $this->title,
                'description' => $this->meta_description ?: config_data_load(config('os_seo'), 'settings.*.description', $wrap['locale']),
                'url'         => $wrap['seo']['base_url'] . $this->generate_url,
            ]
        ];
        $this->setWrap($_set_wrap);
        $_template = [
            "frontend.{$this->deviceTemplate}.nodes.node_page_{$_page_entity->id}",
            "frontend.{$this->deviceTemplate}.nodes.node_page_{$_page_entity->type}",
            "frontend.{$this->deviceTemplate}.nodes.node_{$this->id}",
            "frontend.{$this->deviceTemplate}.nodes.node",
            "backend.base.node_page_{$_page_entity->type}",
            'backend.base.node'
        ];
        if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
        $this->template = $_template;
        switch ($this->page_id) {
            case 18:
                $this->show = Node::_show();
                break;

        }
        return $this;
    }


    public static function getNodeSlider()
    {
//        $_cache_key = cache_key('node', 'node_slider');
//
//        return Cache::remember($_cache_key, REMEMBER_LIFETIME * 24, function () {
            global $wrap;
            $_nodes = self::where('page_id', 13)
                ->active()

                ->with([
                    '_alias',
                    '_preview'
                ])
                ->orderBy('sort')
                ->take(3)
                ->get();

            if ($_nodes->isNotEmpty()) {
                $_template = [
//                    "frontend.{$wrap['device']['template']}.partials.doctors_slider",
                    "frontend.default.load_entities.page_last_nodes",
                ];


                return View::first($_template, ['_items' => $_nodes])->render(function ($view, $content) {

                });
            }

//            return NULL;
//        });
    }

//    public function _link_node()
//    {
//        return Node::where('page_id', 13)
//            ->orderBy('sort')
//            ->remember(REMEMBER_LIFETIME)
//            ->get();
//    }
//
    public function _last_nodes($exclude = [13])
    {
        $_items = $this;
        if (is_array($exclude)) $_items->whereNotIn('id', $exclude);
        $_items = $_items->active()
            ->visibleOnBlock()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        if ($_items->isNotEmpty()) {
            $_items = $_items->map(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');
                return $_item;
            });
        }

        return $_items;
    }

    public function _show()
    {
        // get previous user id
        //$previous = Node::where('id', '<', $this->id)->max('id');

        $previous = Node::where('id', '<', $this->id)->whereIn('page_id', [13])->orderBy('id', 'desc')->first();

//            if($previous->page_id != 18) {
//                $previous = null;
//            }

        // get next user id
        //  $next = Node::where('id', '>', $this->id)->min('id');
        $next = Node::where('id', '>', $this->id)->whereIn('page_id', [13])->first();



        return view::make('frontend.default.nodes.node_page_13')->with('previous', $previous)->with('next', $next);

    }

}
