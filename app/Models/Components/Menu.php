<?php

namespace App\Models\Components;

use App\Library\BaseModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Menu extends BaseModel
{

    protected $table = 'menus';
    protected $guarded = [];
    public $timestamps = FALSE;
    public $menu_items;
    public $render_index;

    public function __construct()
    {
        parent::__construct();
    }

    public function _items()
    {
        return $this->hasMany(MenuItems::class, 'menu_id')
            ->with([
                '_children',
                '_alias'
            ])
            ->orderBy('sort')
            ->orderBy('title');
    }

    public function _items_tree_render($items, $parent = NULL, $options = NULL)
    {
        if ($items->isNotEmpty()) {
            $parents = collect([]);
            foreach ($items as $_item) {
                if (is_null($parent) && $_item->parent_id) continue;
                $_children = $_item->_children()
                    ->where('status', 1)
                    ->get();
                $_item_data = unserialize($_item->data);
                $_item_icon = $_item->_icon_asset();
                $_item_icon_path = $_item->icon_fid ? image_render($_item->_icon, 'thumb_384_500', ['attributes' => ['alt' => $_item->title, 'uk-cover' => true]]) : NULL;
                $_item_preview_500 = $_item->preview_fid ? $_item->_preview_asset('thumb_384_500', ['only_way' => FALSE, 'attributes' => ['alt' => $_item->title, 'uk-cover' => true]]) : NULL;
                $_item_preview_385 = $_item->preview_fid ? $_item->_preview_asset('thumb_455_385', ['only_way' => FALSE, 'attributes' => ['alt' => $_item->title, 'uk-cover' => true]]) : NULL;
                $_preview = $_item->_preview_asset('thumb_455_385', ['only_way' => TRUE]);
                $_item_url_alias = NULL;
                $_item_path = NULL;
                $_item_url = NULL;
                if (isset($_item->_alias->id) && ($_item_url = $_item->_alias)) {
                    $_item_path = $_item->generate_url;
                } elseif ($_item->link) {
                    switch ($_item->link) {
                        case '<front>':
                            $_item_path = _u(LaravelLocalization::getLocalizedURL($this->front_locale, '/'));
                            break;
                        case '<none>':
                            if ($_item->anchor) {
                                $_item_path = '/' . trim(request()->path(), '/');
                            }
                            break;
                        default:
                            $_item_path = $_item->link;
                            break;
                    }
                }
                if ($_item->anchor && $_item_path) $_item_path .= "#{$_item->anchor}";
                $_options = [
                    'entity'   => $_item,
                    'item'     => [
                        'url_alias'       => $_item_url,
                        'icon'            => $_item_icon,
                        'preview'         => $_preview,
                        'icon_path'       => $_item_icon_path,
                        'preview_500'     => $_item_preview_500,
                        'preview_385'     => $_item_preview_385,
                        'title'           => $_item->title,
                        'description'     => $_item->sub_title,
                        'path'            => $_item_path,
                        'anchor'          => $_item->anchor,
                        'active'          => FALSE,
                        'children_active' => FALSE,
                        'wrapper'         => [
                            'class' => [
                                ' uk-menu-item ' . $_item_data['item_class'],
                                count($_children) ? 'uk-menu-item-parent uk-parent' : NULL
                            ]
                        ],
                        'attributes'      => [
                            'class'     => [
                                $_item_data['item_class']
                            ],
                            'id'        => $_item_data['id'],
                            'data-item' => $_item->id,
                            'href'      => $_item_path,
                            'title'     => $_item->title
                        ],
                        'prefix'          => $_item_data['prefix'],
                        'suffix'          => $_item_data['suffix'],
                    ],
                    'children' => (count($_children) ? collect($this->_items_tree_render($_children, $_item->id, $options)) : collect())
                ];
                if ($_item_data['attributes']) $_options['item']['attributes'][] = $_item_data['attributes'];
                $parents->put($_item->id, $_options);
            }

            return $parents;
        } else {
            return $items;
        }
    }

    public function _load($options = [])
    {
        global $wrap;
        $_options = array_merge([
            'view'  => NULL,
            'index' => NULL,
        ], $options);
        if (isset($_options['index']) && $_options['index']) $this->render_index = $_options['index'];
        if ($this->render_index && $this->style_id) $this->style_id .= "-{$this->render_index}";
        $_entity = $this;
        $this->menu_items = Cache::remember("menu_{$this->id}_{$this->frontLocale}", REMEMBER_LIFETIME * 24 * 7, function () use ($_entity, $options) {
            $_items = $_entity->_items()
                ->where('status', 1)
                ->get();

            return $_entity->_items_tree_render($_items, NULL, $options);
        });
        if ($this->menu_items->isNotEmpty()) {
            $_canonical = $wrap['seo']['canonical'];
            $this->menu_items->transform(function ($_item) use ($_canonical) {
                $this->_menu_item_state($_item, $_canonical);

                return $_item;
            });
        }
    }

    public function _render($options = NULL)
    {
        if ($this->invisible) return NULL;
        $_options = array_merge([
            'view'  => NULL,
            'index' => NULL,
        ], $options);
        $this->_load($_options);
        if ($this->menu_items->isNotEmpty()) {
            $_template = [
                "frontend.{$this->deviceTemplate}.menus.menu_{$this->id}",
                "frontend.{$this->deviceTemplate}.menus.menu",
                'backend.base.menu'
            ];
            if (isset($_options['view']) && $_options['view']) array_unshift($_template, $_options['view']);
            $_item = $this;

            return View::first($_template, compact('_item'))
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }

        return NULL;
    }

    public function _menu_item_state(&$item, $canonical)
    {
        $_item_active = $item['item']['anchor'] ? FALSE : active_path($item['item']['path']);
        $_other_item_active = $canonical == $item['item']['path'] ? TRUE : FALSE;
        $item['item']['active'] = $_item_active;
        if ($item['item']['attributes']['id'] && $this->render_index) $item['item']['attributes']['id'] .= "-{$this->render_index}";
        if ($_item_active || $_other_item_active) {
            $item['item']['wrapper']['class'][] = 'uk-active';
            $item['item']['attributes']['class'][] = 'uk-active';
        }
        if ($item['children']) {
            $item['children']->transform(function ($_children) use (&$item, $canonical) {
                $this->_menu_item_state($_children, $canonical);
                if ($_children['item']['active']) {
                    $item['item']['children_active'] = TRUE;
                    $item['item']['wrapper']['class'][] = 'uk-children-active';
                    $item['item']['attributes']['class'][] = 'uk-children-active';
                }

                return $_children;
            });
        }
    }

}
