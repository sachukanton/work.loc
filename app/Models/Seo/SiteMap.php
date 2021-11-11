<?php

namespace App\Models\Seo;

use App\Library\BaseModel;
use App\Models\Pharm\PharmDrug;
use App\Models\Seo\UrlAlias;
use App\Models\Shop\Product;
use App\Models\Structure\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SiteMap extends BaseModel
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function _tree()
    {
        $_locale = wrap()->getLocale();

        return Cache::remember("sitemap_{$_locale}", REMEMBER_LIFETIME * 24, function () use ($_locale) {
            $_items = collect([]);
            if ($_page_front = Page::where('type', 'front')
                ->active()
                ->first()) {
                $_items->push([
                    'name'  => $_page_front->title,
                    'url'   => $_page_front->generate_url,
                    'items' => collect([])
                ]);
            }
            $_url_items = UrlAlias::where('model_type', 'not like', '%Product')
                ->where('model_type', 'not like', '%Node')
                ->where('model_type', 'not like', '%FilterPage')
                ->with([
                    'model'
                ])
                ->where('sitemap', 1)
                ->remember(REMEMBER_LIFETIME)
                ->get([
                    'id',
                    'model_id',
                    'model_type',
                ]);
            if ($_url_items->isNotEmpty()) {
                $_url_items = $_url_items->groupBy('model_type');
                $_url_items->map(function ($_aliases, $_model_type) use (&$_items) {
                    $_class_name = strtolower(class_basename((new $_model_type)->getMorphClass()));
                    $_aliases->map(function ($_alias) use (&$_items, $_class_name) {
                        $_model = $_alias->model;
                        switch ($_class_name) {
                            case 'page':
                            case 'tag':
                                $_model_items = collect([]);
                                $_nodes = $_model->_nodes()
                                    ->get([
                                        'id',
                                        'title',
                                    ]);
                                if ($_nodes->isNotEmpty()) {
                                    $_nodes->map(function ($_node) use (&$_model_items) {
                                        $_model_items->push([
                                            'name'  => $_node->title,
                                            'url'   => $_node->generate_url,
                                            'items' => NULL
                                        ]);
                                    });
                                }
                                $_items->push([
                                    'name'  => $_model->title,
                                    'url'   => $_model->generate_url,
                                    'items' => $_model_items
                                ]);
                                break;
                            case 'category':
                                $_model_items = collect([]);
                                $_filter_pages = $_model->_filter_pages()
                                    ->with([
                                        'model'
                                    ])
                                    ->get();
                                if ($_filter_pages->isNotEmpty()) {
                                    $_filter_pages->map(function ($_alias) use (&$_model_items) {
                                        $_model = $_alias->model;
                                        $_model_items->push([
                                            'name'  => $_model->title,
                                            'url'   => $_model->generate_url,
                                            'items' => NULL
                                        ]);
                                    });
                                }
                                $_items->push([
                                    'name'  => $_model->title,
                                    'url'   => $_model->generate_url,
                                    'items' => $_model_items
                                ]);
                                break;
                            default:
                                $_items->push([
                                    'name'  => $_model->title,
                                    'url'   => $_model->generate_url,
                                    'items' => collect([])
                                ]);
                                break;
                        }
                    });
                });
            }

            return $_items;
        });
    }

    public static function _list($full = TRUE)
    {
        $_items = collect([]);
        $_page_fronts = Page::where('type', 'front')
            ->active()
            ->remember(REMEMBER_LIFETIME)
            ->get([
                'title',
                'updated_at'
            ]);
        if ($_page_fronts) {
            $_page_fronts->map(function ($_page) use (&$_items) {
                $_items->push([
                    'name'          => $_page->title,
                    'url'           => $_page->generate_url,
                    'last_modified' => Carbon::parse(config('os_seo.last_modified_timestamp'))->format('c'),
                    'items'         => NULL,
                    'changefreq'    => 'always',
                    'priority'      => 0.5,
                ]);
            });
        }
        if ($full) {
            $_url_items = UrlAlias::where('model_type', 'not like', '%Product')
                ->with([
                    'model'
                ])
                ->where('sitemap', 1)
                ->remember(REMEMBER_LIFETIME)
                ->get([
                    'id',
                    'alias',
                    'model_id',
                    'model_type',
                    'changefreq',
                    'priority',
                ]);
            if ($_url_items->isNotEmpty()) {
                $_date_now = Carbon::now();
                $_url_items = $_url_items->groupBy('model_type');
                $_url_items->map(function ($_aliases, $_model_type) use (&$_items, $_date_now) {
                    $_aliases->map(function ($_alias) use (&$_items, $_date_now) {
                        if (($_model = $_alias->model) && (($_model->hasAttribute('status') && $_model->status) || !$_model->hasAttribute('status'))) {
                            $_items->push([
                                'name'          => $_model->title,
                                'url'           => $_model->generate_url,
                                'last_modified' => $_model->updated_at ? $_model->updated_at->format('c') : NULL,
                                'items'         => NULL,
                                'changefreq'    => $_alias->changefreq,
                                'priority'      => $_alias->priority,
                            ]);
                        }
                    });
                });
            }
        }

        return $_items;
    }

    public static function _renderXML($index = NULL)
    {
        if ($index) {
            $_items = collect([]);
            $_parse = explode('-', $index);
            if (isset($_parse[0])) {
                switch ($_parse[0]) {
                    case 'products':
                        $_offset = $_parse[1] ?? 0;
                        $_products = Product::from('shop_products as p')
                            ->leftJoin('url_alias as a', 'a.model_id', '=', 'p.id')
                            ->where('a.model_type', 'like', '%Product')
                            ->where('p.status', 1)
                            ->where('a.sitemap', 1)
                            ->with([
                                '_alias'
                            ])
                            ->offset($_offset)
                            ->limit(1500)
                            ->remember(REMEMBER_LIFETIME)
                            ->get([
                                'p.title',
                                'p.id',
                                'p.updated_at',
                            ]);
                        if ($_products->isNotEmpty()) {
                            $_products->map(function ($_item) use (&$_items) {
                                $_alias = $_item->_alias;
                                $_items->push([
                                    'name'          => $_item->title,
                                    'url'           => $_item->generate_url,
                                    'last_modified' => $_item->updated_at ? $_item->updated_at->format('c') : NULL,
                                    'items'         => NULL,
                                    'changefreq'    => $_alias->changefreq,
                                    'priority'      => $_alias->priority,
                                ]);
                            });
                        }
                        break;
                    default:
                        $_items = self::_list();
                        break;
                }
            }
            $xmlDom = new \DOMDocument("1.0", "utf-8");
            $urlSet = $xmlDom->createElement('urlset');
            $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlSet = $xmlDom->appendChild($urlSet);
            if ($_items) {
                $_base_url = trim(config('app.url'), '/');
                $_items->map(function ($item) use (&$urlSet, $xmlDom, $_base_url) {
                    $url = $xmlDom->createElement('url');
                    $url = $urlSet->appendChild($url);
                    $loc = $xmlDom->createElement('loc');
                    $loc = $url->appendChild($loc);
                    $loc->appendChild($xmlDom->createTextNode($_base_url . $item['url']));
                    $lastmod = $xmlDom->createElement('lastmod');
                    $lastmod = $url->appendChild($lastmod);
                    $lastmod->appendChild($xmlDom->createTextNode($item['last_modified']));
                    if (isset($item['changefreq'])) {
                        $changefreq = $xmlDom->createElement('changefreq');
                        $changefreq = $url->appendChild($changefreq);
                        $changefreq->appendChild($xmlDom->createTextNode($item['changefreq']));
                    }
                    if (isset($item['priority'])) {
                        $priority = $xmlDom->createElement('priority');
                        $priority = $url->appendChild($priority);
                        $priority->appendChild($xmlDom->createTextNode($item['priority']));
                    }
                });
            }
            $xmlDom->formatOutput = TRUE;
            $siteMapXML = $xmlDom->saveXML();

        } else {
            $_items = collect([]);
            $_items->push([
                'name'          => NULL,
                'url'           => _u("sitemap-general.xml"),
                'last_modified' => Carbon::now()->format('c'),
                'items'         => NULL
            ]);
            $_products_count = Product::from('shop_products as p')
                ->leftJoin('url_alias as a', 'a.model_id', '=', 'p.id')
                ->where('a.model_type', 'like', '%Product')
                ->where('p.status', 1)
                ->where('a.sitemap', 1)
                ->count();
            $_count_index = ceil($_products_count / 1500);
            for ($i = 0; $i < $_count_index; $i++) {
                $_items->push([
                    'name'          => NULL,
                    'url'           => _u("sitemap-products-{$i}.xml"),
                    'last_modified' => Carbon::now()->format('c'),
                    'items'         => NULL
                ]);
            }
            $xmlDom = new \DOMDocument("1.0", "utf-8");
            $urlSet = $xmlDom->createElement('sitemapindex');
            $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $urlSet = $xmlDom->appendChild($urlSet);
            if ($_items) {
                $_base_url = trim(config('app.url'), '/');
                $_items->map(function ($item) use (&$urlSet, $xmlDom, $_base_url) {
                    $url = $xmlDom->createElement('sitemap');
                    $url = $urlSet->appendChild($url);
                    $loc = $xmlDom->createElement('loc');
                    $loc = $url->appendChild($loc);
                    $loc->appendChild($xmlDom->createTextNode($_base_url . $item['url']));
                    $lastmod = $xmlDom->createElement('lastmod');
                    $lastmod = $url->appendChild($lastmod);
                    $lastmod->appendChild($xmlDom->createTextNode($item['last_modified']));
                    if (isset($item['changefreq'])) {
                        $changefreq = $xmlDom->createElement('changefreq');
                        $changefreq = $url->appendChild($changefreq);
                        $changefreq->appendChild($xmlDom->createTextNode($item['changefreq']));
                    }
                    if (isset($item['priority'])) {
                        $priority = $xmlDom->createElement('priority');
                        $priority = $url->appendChild($priority);
                        $priority->appendChild($xmlDom->createTextNode($item['priority']));
                    }
                });
            }
            $xmlDom->formatOutput = TRUE;
            $siteMapXML = $xmlDom->saveXML();
        }
        header('Content-Type: text/xml; charset=UTF-8', TRUE, 200);
        echo $siteMapXML;
        exit;
    }

}
