<?php

namespace App\Models\Structure;

use App\Library\BaseModel;
use App\Models\Seo\UrlAlias;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class Tag extends BaseModel
{

    protected $entity;
    protected $table = 'tags';
    protected $guarded = [];
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
    protected $perPage = 12;

    public function __construct($entity = NULL)
    {
        parent::__construct();
        $this->entity = $entity;
    }

    public function _nodes()
    {
        return $this->morphedByMany(Node::class, 'model', 'taggables')
            ->where('visible_on_list', 1)
            ->with([
                '_alias'   => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_page'    => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_user'    => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_preview' => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                }
            ])
            ->remember(REMEMBER_LIFETIME);
    }

    public function _parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id')
            ->with([
                '_parent',
                '_children'
            ]);
    }

    public function _children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with([
                '_children'
            ]);
    }

    public function set()
    {
        $_response = NULL;
        $_tags = request()->input('tags');
        if ($this->entity) {
            $this->entity->_tags()->detach();
            if ($_tags) {
                $_attach = [];
                foreach ($_tags as $_tag) {
                    if (ctype_digit($_tag)) {
                        $_attach[] = $_tag;
                    } else {
                        $_item = new self;
                        $_item->title = $_tag;
                        $_item->save();
                        $_generate_alias = generate_alias($_item->title);
                        if (UrlAlias::where('alias', $_generate_alias)
                                ->count() > 0
                        ) {
                            $index = 0;
                            while ($index <= 100) {
                                $_generate_url = "{$_generate_alias}-{$index}";
                                if (UrlAlias::where('alias', $_generate_url)
                                        ->count() == 0
                                ) {
                                    $_generate_alias = $_generate_url;
                                    break;
                                }
                                $index++;
                            }
                        }
                        $_alias = new UrlAlias;
                        $_alias->fill([
                            'alias'               => $_generate_alias,
                            'changefreq'          => 'monthly',
                            'sitemap'             => 1,
                            'priority'            => 0.5,
                            'model_default_title' => $_item->getTranslation('title', $this->defaultLocale),
                        ]);
                        $_item->_alias()->save($_alias);
                        $_attach[] = $_item->id;
                    }
                }
                $this->entity->_tags()->attach($_attach);
            }
        }
    }

    public function _last_nodes($take = 5, $exclude = [])
    {
        $_items = $this->_nodes();
        if (is_array($exclude)) $_items->whereNotIn('id', $exclude);
        $_items = $_items->active()
            ->visibleOnBlock()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take($take)
            ->get();
        if ($_items->isNotEmpty()) {
            $_items = $_items->map(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
        }

        return $_items;
    }

    public function _load($view_mode = 'full')
    {
        switch ($view_mode) {
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
        $this->_load($_view);
        $_set_wrap = [
            'seo.title'         => $this->meta_title ? : $this->title,
            'seo.keywords'      => $this->meta_keywords,
            'seo.description'   => $this->meta_description,
            'seo.robots'        => $this->meta_robots,
            'seo.last_modified' => $this->last_modified,
            'page.title'        => $this->title,
            'page.style_id'     => $this->style_id,
            'page.style_class'  => $this->style_class ? [$this->style_class] : NULL,
            'page.breadcrumb'   => breadcrumb_render(['entity' => $this]),
            'seo.open_graph'    => [
                'title'       => $this->title,
                'description' => $this->meta_description ?: config_data_load(config('os_seo'), 'settings.*.description', $wrap['locale']),
                'url'         => $wrap['seo']['base_url'] . $this->generate_url,
            ]
        ];
        $_page_number = current_page();
        $_items = $this->_nodes()
            ->select([
                'id',
                'title',
                'page_id',
                'preview_fid',
                'sort',
                'published_at',
                'teaser',
                'body'
            ])
            ->active()
            ->visibleOnList()
            ->orderBy('sort')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');
        if ($_page_number) {
            Paginator::currentPageResolver(function () use ($_page_number) {
                return $_page_number;
            });
        }
        $_items = $_items
            ->paginate($this->perPage)
            ->onEachSide(1);
        $_items->getCollection()->transform(function ($_item) {
            if (method_exists($_item, '_load')) $_item->_load('teaser');

            return $_item;
        });
        $this->_items = $_items;
        if ($this->_items->isEmpty() && $_page_number) abort(404);
        if ($_page_number) $_set_wrap['seo.robots'] = 'noindex, follow';
        if ($this->_items->isNotEmpty() && $this->_items->hasMorePages()) {
            $_page_number = $_page_number ? : 1;
            $_page_number++;
            $_current_url = $wrap['seo']['url_alias'];
            $_current_url_query = $wrap['seo']['url_query'];
            $_url = trim($_current_url, '/') . "/page-{$_page_number}";
            $_next_page_link = _u($_url) . $_current_url_query;
            $_set_wrap['seo.link_next'] = $_next_page_link;
        }
        $this->setWrap($_set_wrap);
        $_template = [
            "frontend.{$this->deviceTemplate}.pages.tag_{$this->id}",
            "frontend.{$this->deviceTemplate}.pages.tag",
            "frontend.default.pages.tag_{$this->id}",
            "frontend.default.pages.tag",
            'backend.base.page_tag'
        ];
        if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
        $this->template = $_template;

        return $this;
    }

    public function _render_ajax(Request $request)
    {
        $this->_load();
        $_items = NULL;
        $_load_more = $request->has('load_more') ? TRUE : FALSE;
        if ($_load_more == FALSE) {
            return [
                'commands' => [
                    [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => trans('frontend.notice_an_error_has_occurred'),
                            'status' => 'danger',
                        ]
                    ]
                ]
            ];
        }
        $_set_wrap = [];
        $_page_number = current_page();
        $_items = $this->_nodes()
            ->select([
                'id',
                'title',
                'page_id',
                'preview_fid',
                'sort',
                'published_at',
                'teaser',
                'body'
            ])
            ->active()
            ->orderBy('sort')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');
        if ($_page_number) {
            Paginator::currentPageResolver(function () use ($_page_number) {
                return $_page_number;
            });
        }
        $_items = $_items->paginate($this->perPage);
        $_items->getCollection()->transform(function ($_item) {
            if (method_exists($_item, '_load')) $_item->_load('teaser');

            return $_item;
        });
        if ($_items->isNotEmpty()) {
            $_items->getCollection()->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
            //                if ($_items->hasMorePages()) {
            //                    $_page_number = $_page_number ? : 1;
            //                    $_page_number++;
            //                    $_current_url = wrap()->get('seo.url_alias');
            //                    $_current_url_query = wrap()->get('seo.url_query');
            //                    $_url = trim($_current_url, '/') . "/page-{$_page_number}";
            //                    $_next_page_link = _u($_url) . $_current_url_query;
            //                    $_set_wrap['seo.link_next'] = $_next_page_link;
            //                } elseif ($_items->lastPage() == $_items->currentPage()) {
            //                    $_set_wrap['seo.link_next'] = FALSE;
            //                }
        }
        $this->setWrap($_set_wrap);
        if ($_load_more) {
            $_items_output = NULL;
            $_item_template = [
                "frontend.{$this->deviceTemplate}.nodes.node_teaser_{$this->id}",
                "frontend.{$this->deviceTemplate}.nodes.node_teaser",
                "frontend.default.nodes.node_teaser_{$this->id}",
                "frontend.default.nodes.node_teaser",
                'backend.base.node_teaser'
            ];
            foreach ($_items as $_item) {
                $_items_output .= View::first($_item_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
            $commands['commands'][] = [
                'command' => 'append',
                'options' => [
                    'target' => '#uk-items-list',
                    'data'   => clear_html($_items_output)
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-pagination',
                    'data'   => clear_html($_items->links('backend.base.pagination'))
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-body',
                    'data'   => ''
                ]
            ];
        }

        return $commands;
    }

    public static function tags_tree_render($selected = NULL)
    {
        $_response = [];
        $_tags = self::where('status', 1)
            ->with([
                '_parent',
                '_children',
                '_nodes',
            ])
            ->orderBy('sort')
            ->distinct()
            ->remember(REMEMBER_LIFETIME)
            ->select([
                'id',
                'parent_id',
                'title',
            ])
            ->get();
        if ($_tags->isNotEmpty()) {
            $_tags->each(function ($_item) use (&$_response, $_tags, $selected) {
                if (is_null($_item->parent_id)) {
                    $_item_nodes_count = $_item->_nodes->count();
                    $_children_nodes_count = 0;
                    $_children_active = FALSE;
                    $_children = self::tree_parents_tag($_tags, $_item->id, $selected);
                    if ($_children) {
                        $_children_active = array_filter($_children, function ($_innerArray) use (&$_children_nodes_count) {
                            if ($_innerArray['nodes']) $_children_nodes_count += $_innerArray['nodes'];

                            return ($_innerArray['active'] == TRUE);
                        });
                    }
                    $_data = [
                        'id'              => $_item->id,
                        'title'           => $_item->getTranslation('title', app()->getLocale()),
                        'alias'           => $_item->generate_url,
                        'children'        => $_children,
                        'nodes'           => $_item_nodes_count >= $_children_nodes_count ? $_item_nodes_count : $_children_nodes_count,
                        'active'          => $_item->id == $selected ? TRUE : FALSE,
                        'children_active' => (boolean)$_children_active
                    ];
                    $_response[$_item->id] = $_data;
                }
            });
        }

        return $_response;
    }

    public static function tree_parents_tag(&$tags, $parent = NULL, $selected)
    {
        $_response = NULL;
        $tags->each(function ($_item) use (&$_response, $tags, $parent, $selected) {
            if ($parent && $_item->parent_id == $parent) {
                $_item_nodes_count = $_item->_nodes->count();
                $_children_nodes_count = 0;
                $_children_active = FALSE;
                $_children = self::tree_parents_tag($tags, $_item->id, $selected);
                if ($_children) {
                    $_children_active = array_filter($_children, function ($_innerArray) use (&$_children_nodes_count) {
                        if ($_innerArray['nodes']) $_children_nodes_count += $_innerArray['nodes'];

                        return ($_innerArray['active'] == TRUE);
                    });
                }
                $_data = [
                    'id'              => $_item->id,
                    'title'           => $_item->getTranslation('title', app()->getLocale()),
                    'alias'           => $_item->generate_url,
                    'children'        => $_children,
                    'nodes'           => $_item_nodes_count >= $_children_nodes_count ? $_item_nodes_count : $_children_nodes_count,
                    'active'          => $_item->id == $selected ? TRUE : FALSE,
                    'children_active' => (boolean)$_children_active
                ];
                $_response[$_item->id] = $_data;
            }
        });

        return $_response;
    }

    public function getSchemaAttribute()
    {
        global $wrap;
        $_response = [
            "@context"    => "https://schema.org",
            "@type"       => "WebPage",
            "name"        => $this->title,
            "description" => "",
            "publisher"   => [
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

}
