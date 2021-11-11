<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Models\Seo\UrlAlias;

class ParamItem extends BaseModel
{

    protected $table = 'shop_param_items';
    protected $guarded = [];
    public $timestamps = FALSE;
    public $translatable = [
        'title',
        'sub_title',
        'meta_title',
        'unit_value'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Relationships
     */
    public function _param()
    {
        return $this->belongsTo(Param::class, 'param_id');
    }

    public function get_count_products($categories, $query, $param)
    {
        $_response = [
            'count'          => 0,
            'alias'          => NULL,
            'alias_rollback' => NULL,
            'active'         => FALSE,
        ];
        if ($query['use']) {
            if (isset($query['where']['and'][$param->id][$this->id]) || isset($query['where']['or'][$param->id][$this->id])) {
                $_response['active'] = TRUE;
                if (isset($query['where']['and'][$param->id][$this->id])) {
                    unset($query['where']['and'][$param->id][$this->id]);
                } elseif (isset($query['where']['or'][$param->id][$this->id])) {
                    unset($query['where']['or'][$param->id][$this->id]);
                    if (count($query['where']['or'][$param->id]) == 1) {
                        $query['where']['and'][$param->id] = $query['where']['or'][$param->id];
                        unset($query['where']['or'][$param->id]);
                    } elseif (count($query['where']['or'][$param->id]) == 0) {
                        unset($query['where']['or'][$param->id]);
                    }
                }
            } else {
                if (isset($query['where']['and'][$param->id]) && !isset($query['where']['and'][$param->id][$this->id]) && $param->condition == 'and') {
                    $query['where']['and'][$param->id][$this->id] = $this->name;
                } elseif (isset($query['where']['and'][$param->id]) && !isset($query['where']['and'][$param->id][$this->id]) && $param->condition == 'or') {
                    $query['where']['or'][$param->id] = $query['where']['and'][$param->id];
                    $query['where']['or'][$param->id][$this->id] = $this->name;
                    unset($query['where']['and'][$param->id]);
                } elseif (isset($query['where']['or'][$param->id]) && !isset($query['where']['or'][$param->id][$this->id])) {
                    $query['where']['or'][$param->id][$this->id] = $this->name;
                } else {
                    $query['where']['and'][$param->id][$this->id] = $this->name;
                }
            }
        } else {
            $query['where']['and'][$param->id][$this->id] = $this->name;
        }
        $_to_collect = [];
        if ($query['where']['and']) {
            foreach ($query['where']['and'] as $_where_and) {
                foreach ($_where_and as $_where_value) {
                    $_to_collect[] = $_where_value;
                }
            }
        }
        if ($query['where']['or']) {
            foreach ($query['where']['or'] as $_where_or) {
                $_to_collect[] = $_where_or;
            }
        }
        $query['where']['to_collect'] = $_to_collect;
        $_alias_filter = $this->get_alias_to_filter($query, $_response['active']);
        $_response['count'] = 0;//$this->get_count_product_to_filter($categories, $query, $_response['active']);
        $_response['query'] = $this->get_query_count_product_to_filter($categories, $query, $_response['active']);
        $_response['query_2'] = $this->get_query_count_product_to_filter_2($categories, $query, $_response['active']);
        $_response['alias'] = $_alias_filter['alias'];
        $_response['alias_rollback'] = $_alias_filter['rollback'];
        $_response['base_alias'] = preg_replace("/\?.+/", "", $_alias_filter['alias']);

        return $_response;
    }

    public function get_count_product_to_filter($categories, $query, $rollback = FALSE)
    {
        $_response = Product::from('shop_products as p')
            ->leftJoin('shop_product_category as pc', 'pc.model_id', '=', 'p.id')
            ->leftJoin('shop_brands as b', 'b.id', '=', 'p.brand_id')
            ->whereIn('pc.category_id', $categories)
            ->where('p.status', 1);
        $_param_index = 0;
        if ($query['brands']) $_response->whereIn('b.name', $query['brands']);
        if ($query['where']['to_collect']) {
            foreach ($query['where']['to_collect'] as $_where) {
                $_param_index++;
                $_response->leftJoin("shop_product_param as cp{$_param_index}", "cp{$_param_index}.model_id", '=', 'p.id');
                if (is_array($_where)) {
                    $_response->whereIn("cp{$_param_index}.name", $_where);
                } else {
                    $_response->where("cp{$_param_index}.name", $_where);
                }
                //                    if (is_array($_where)) {
                //                        $_response->whereIn("cp{$_param_index}.name", $_where);
                //                                                } else {
                //                            $_response->where("cp{$_param_index}.name", $_where);
                //                    }
            }
        }
        if ($query['price']) {
            // todo: дописать для поиска по цене
        }
        if ($query['where']['between']) {
            foreach ($query['where']['between'] as $_param_name => $_param_value) {
                $_param_index++;
                $_response->leftJoin("shop_product_param as cp{$_param_index}", "cp{$_param_index}.model_id", '=', 'p.id');
                $_response->whereBetween("cp{$_param_index}.value", [
                    $_param_value['min'],
                    $_param_value['max']
                ]);
            }
        }
        $_response = $_response->distinct()
            ->remember(REMEMBER_LIFETIME)
            ->count();

        return $_response;
    }

    public function get_alias_to_filter($query, $rollback = FALSE)
    {
        $_response = [
            'alias'    => NULL,
            'rollback' => NULL,
        ];
        if ($query['where']['and']) {
            ksort($query['where']['and']);
            foreach ($query['where']['and'] as $_key => $_query_and) {
                $_tmp = [];
                $_tmp_rollback = [];
                ksort($_query_and);
                foreach ($_query_and as $_sub_query_and) {
                    $_tmp[] = $_sub_query_and;
                    if ($rollback && $_sub_query_and != $this->name) $_tmp_rollback[] = $_sub_query_and;
                }
                if ($_tmp) $_response['alias'][$_key] = implode('-and-', $_tmp);
                if ($_tmp_rollback) $_response['rollback'][$_key] = implode('-and-', $_tmp_rollback);
            }
        }
        if ($query['where']['or']) {
            ksort($query['where']['or']);
            foreach ($query['where']['or'] as $_key => $_query_or) {
                $_tmp = [];
                $_tmp_rollback = [];
                ksort($_query_or);
                foreach ($_query_or as $_key_2 => $_sub_query_or) {
                    $_tmp[] = $_sub_query_or;
                    if ($rollback && $_sub_query_or != $this->name) $_tmp_rollback[] = $_sub_query_or;
                }
                if ($_tmp) $_response['alias'][$_key] = implode('-or-', $_tmp);
                if ($_tmp_rollback) $_response['rollback'][$_key] = implode('-or-', $_tmp_rollback);
            }
        }
        if (is_array($_response['alias'])) ksort($_response['alias']);
        if (is_array($_response['rollback'])) ksort($_response['rollback']);
        $_response_alias = NULL;
        $_response_rollback = NULL;
        if ($query['brands']) {
            sort($query['brands']);
            $_response_alias = 'brands-' . implode('-or-', $query['brands']);
            if ($rollback) $_response_rollback = 'brands-' . implode('-or-', $query['brands']);
        }
        if ($_response['alias']) $_response_alias .= ($_response_alias ? '-&-' : NULL) . implode('-&-', $_response['alias']);
        if ($_response['rollback']) $_response_rollback .= ($_response_rollback ? '-&-' : NULL) . implode('-&-', $_response['rollback']);
        $_query_string = NULL;
        if ($query['price']) $_query_string[] = "price[min]={$query['price']['min']}&price[max]={$query['price']['max']}";
        if ($query['where']['between']) foreach ($query['where']['between'] as $_key => $_query) $_query_string[] = "{$_key}[min]={$_query['min']}&{$_key}[max]={$_query['max']}";
        if ($query['sort']) $_query_string[] = "sort={$query['sort']}";
        if ($_query_string) {
            $_response_alias .= '?' . implode('&', $_query_string);
            if ($rollback) $_response_rollback .= '?' . implode('&', $_query_string);
        }
        $_response['alias'] = $_response_alias;
        $_response['rollback'] = $_response_rollback;

        return $_response;
    }

    public static function get_count_product_brand($categories, $query, $brand)
    {
        $_response = [
            'count'          => 0,
            'alias'          => NULL,
            'alias_rollback' => NULL,
            'active'         => FALSE,
        ];
        if (is_array($query['brands']) && in_array($brand->name, $query['brands'])) {
            $_response['active'] = TRUE;
            $_brands = [];
            foreach ($query['brands'] as $_brand) if ($_brand != $brand->name) $_brands[] = $_brand;
            $query['brands'] = $_brands;

        } else {
            $query['brands'][] = $brand->name;
        }
        $_to_collect = [];
        if ($query['where']['and']) {
            foreach ($query['where']['and'] as $_where_and) {
                foreach ($_where_and as $_where_value) {
                    $_to_collect[] = $_where_value;
                }
            }
        }
        if ($query['where']['or']) {
            foreach ($query['where']['or'] as $_where_or) {
                $_to_collect[] = $_where_or;
            }
        }
        $query['to_collect'] = $_to_collect;
        $_param_item = new self();
        $_alias_filter = $_param_item->get_alias_to_filter_brand($query, $_response['active'], $brand);
        $_response['count'] = 0;//$_param_item->get_count_product_to_filter($categories, $query, $_response['active']);
        $_response['query'] = $_param_item->get_query_count_product_to_filter($categories, $query, $_response['active']);
        $_response['query_2'] = $_param_item->get_query_count_product_to_filter_2($categories, $query, $_response['active']);
        $_response['alias'] = $_alias_filter['alias'];
        $_response['alias_rollback'] = $_alias_filter['rollback'];
        $_response['base_alias'] = preg_replace("/\?.+/", "", $_alias_filter['alias']);

        return $_response;
    }

    public function get_alias_to_filter_brand($query, $rollback = FALSE, $brand)
    {
        $_response = [
            'alias'    => NULL,
            'rollback' => NULL,
        ];
        if ($query['where']['and']) {
            ksort($query['where']['and']);
            foreach ($query['where']['and'] as $_query_and) {
                ksort($_query_and);
                foreach ($_query_and as $_sub_query_and) {
                    $_response['alias'][] = $_sub_query_and;
                    if ($rollback) $_response['rollback'][] = $_sub_query_and;
                }
            }
        }
        if ($query['where']['or']) {
            ksort($query['where']['or']);
            foreach ($query['where']['or'] as $_query_or) {
                $_tmp = [];
                $_tmp_rollback = [];
                ksort($_query_or);
                foreach ($_query_or as $_sub_query_or) {
                    $_tmp[] = $_sub_query_or;
                    if ($rollback) $_tmp_rollback[] = $_sub_query_or;
                }
                if ($_tmp) $_response['alias'][] = implode('-or-', $_tmp);
                if ($_tmp_rollback) $_response['rollback'][] = implode('-or-', $_tmp_rollback);
            }
        }
        $_response_alias = NULL;
        $_response_rollback = NULL;
        if ($query['brands']) {
            sort($query['brands']);
            $_response_alias = 'brands-' . implode('-or-', $query['brands']);
            if ($rollback) {
                if (($_key = array_search($brand->name, $query['brands'])) !== FALSE) unset($query['brands'][$_key]);
                if ($query['brands']) $_response_rollback = 'brands-' . implode('-or-', $query['brands']);
            }
        }
        if ($_response['alias']) $_response_alias .= '-&-' . implode('-&-', $_response['alias']);
        if ($_response['rollback']) $_response_rollback .= ($_response_rollback ? '-&-' : NULL) . implode('-&-', $_response['rollback']);
        $_query_string = NULL;
        if ($query['price']) $_query_string[] = "price[min]={$query['price']['min']}&price[max]={$query['price']['max']}";
        if ($query['where']['between']) foreach ($query['where']['between'] as $_key => $_query) $_query_string[] = "{$_key}[min]={$_query['min']}&{$_key}[max]={$_query['max']}";
        if ($query['sort']) $_query_string[] = "sort={$query['sort']}";
        if ($_query_string) {
            $_response_alias .= '?' . implode('&', $_query_string);
            if ($rollback) $_response_rollback .= '?' . implode('&', $_query_string);
        }
        $_response['alias'] = $_response_alias;
        $_response['rollback'] = $_response_rollback;

        return $_response;
    }

    public function get_alias_to_page($category)
    {
        $_alias = "{$category->_alias->alias}-cfp-{$this->name}";
        $_filter_pages = $category->_filter_pages
            ->where('base_path', $_alias)
            ->first();
        if ($_filter_pages) $_alias = $_filter_pages->alias;

        return $_alias;
    }

    public function get_query_count_product_to_filter($categories, $query, $rollback = FALSE)
    {
        $_query['begin'][] = "select count(DISTINCT  `p`.`id`) as count from `shop_products` as `p` left join `shop_product_category` as `pc` on `pc`.`model_id` = `p`.`id`";
        $_query['where'][] = "`pc`.`category_id` in (" . $categories->implode(',') . ")";
        $_query['where'][] = "`p`.`status` = 1";
        if ($query['brands']) {
            $_brands = [];
            foreach ($query['brands'] as $_brand) $_brands[] = "'{$_brand}'";
            $_query['where'][] = "`b`.`name` in (" . implode(',', $_brands) . ")";
        }
        $_param_index = 0;
        if ($query['where']['to_collect']) {
            foreach ($query['where']['to_collect'] as $_where) {
                $_param_index++;
                $_query['begin'][] = "left join `shop_product_param` as `cp{$_param_index}` on `cp{$_param_index}`.`model_id` = `p`.`id`";
                if (is_array($_where)) {
                    $_options = [];
                    foreach ($_where as $_option) $_options[] = "'{$_option}'";
                    $_query['where'][] = "`cp{$_param_index}`.`name` in (" . implode(',', $_options) . ")";
                } else {
                    $_query['where'][] = "`cp{$_param_index}`.`name` = '{$_where}'";
                }
            }
        }
        if ($query['price']) {
            $_query['begin'][] = "left join `shop_product_prices` as `pr` on `pr`.`product_id` = `p`.`id`";
            $_query['where'][] = "`pr`.`base_price` >= '{$query['price']['min']}' and `pr`.`base_price` <= '{$query['price']['max']}'";
        }
        if ($query['where']['between']) {
            foreach ($query['where']['between'] as $_param_name => $_param_value) {
                $_param_index++;
                $_query['begin'][] = "left join `shop_product_param` as `cp{$_param_index}` on `cp{$_param_index}`.`model_id` = `p`.`id`";
                $_query['where'][] = "`cp{$_param_index}`.`value` >= '{$query['price']['min']}' and `cp{$_param_index}`.`value` <= '{$query['price']['max']}'";
            }
        }
        $_response = implode(' ', $_query['begin']) . " where " . implode(' and ', $_query['where']);

        return $_response;
    }

    public function get_query_count_product_to_filter_2($categories, $query, $rollback = FALSE)
    {

        $_response = NULL;
        $_query['begin'] = NULL;
        $_param_index = 0;
        if ($query['brands']) {
            $_brands = [];
            foreach ($query['brands'] as $_brand) $_brands[] = "'{$_brand}'";
            $_query['where'][] = "`b`.`name` in (" . implode(',', $_brands) . ")";
        }
        if ($query['where']['to_collect']) {
            foreach ($query['where']['to_collect'] as $_where) {
                if (is_null($_query['begin'])) {
                    $_query['begin'][] = "select count(DISTINCT  `cp0`.`model_id`) from `shop_product_param` as `cp0`";
                    $_query['where'][] = "`cp0`.`model_id` = `p`.`id`";
                } else {
                    $_param_index++;
                    $_query['begin'][] = "left join `shop_product_param` as `cp{$_param_index}` on `cp{$_param_index}`.`model_id` = `cp0`.`model_id`";
                }
                if (is_array($_where)) {
                    $_options = [];
                    foreach ($_where as $_option) $_options[] = "'{$_option}'";
                    $_query['where'][] = "`cp{$_param_index}`.`name` in (" . implode(',', $_options) . ")";
                } else {
                    $_query['where'][] = "`cp{$_param_index}`.`name` = '{$_where}'";
                }
            }
        }
        if (isset($_query['where']) && is_array($_query['where']) && is_array($_query['begin'])) {
            $_response = implode(' ', $_query['begin']) . " where " . implode(' and ', $_query['where']);
        } elseif (isset($_query['where']) && is_array($_query['where'])) {
            $_response = 'select count(`p0`.`id`) from `shop_products` as `p0` where `p0`.`id` = `p`.`id` and' . implode(' and ', $_query['where']);
        }

        return $_response;
    }

//    public function _alias_product()
//    {
//
//        $_param_product_alias = $this->alias;
//
//        $_items = [];
//        if ($_search = $_param_product_alias) {
//
////            $_exists_id = $this->entity::pluck('product_id');
//
//
//            $_items = UrlAlias::from('url_alias as a')
////                ->with([
////                    'model'
////                ])
//                ->where('a.alias', $_param_product_alias)
//                ->where('a.model_type', Product::class)
////                ->when($_exists_id, function ($query) use ($_exists_id) {
////                    $query->whereNotIn('a.model_id', $_exists_id);
////                })
//                ->limit(8)
//                ->get([
//                    'a.model_id',
//                ]);
//            foreach ($_items as $_item_id_product) {
//                $_item_id[] = $_item_id_product['model_id'];
//            }
//
//
//            $_response = Product::from('shop_products as p')
//                ->where('p.id', $_item_id)
////                ->leftJoin('shop_product_prices as sp', 'sp.product_id', '=', $_item_id)
//                ->with([
//                    '_alias',
//                    '_param_items',
//                    '_preview',
//                    '_price'
//                ])
//                ->select([
//                    'p.id',
//                    'p.title',
//                    'p.preview_fid',
//                    'p.full_fid',
//                    'p.mark_hit',
//                    'p.mark_new',
//                    'p.sort',
//                    'p.status',
////                    'sp.id as price_id',
////                    'sp.part as price_part',
////                    'sp.quantity_id as quantity_id',
//                ])
//                ->remember(REMEMBER_LIFETIME)
//                ->get();
////            dd($_response);
////            if ($_items->isNotEmpty()) {
////                $_items = $_items->transform(function ($_item) {
////                    $_model = $_item->model;
////
////                    return [
////                        'name' => $_model->title,
////                        'view' => NULL,
////                        'data' => $_model->id
////                    ];
////
////                })->toArray();
////            }
//        }
//
////        $_item_alias = UrlAlias::where('url_alias', $_param_product_alias)
////            ->get();
////        $_items = UrlAlias::from('url_alias as a')
////            ->where('alias', $_param_product_alias)
////            ->get();
//
//
//
//
//
//
////        $_items = UrlAlias::where('alias', $_param_product_alias)
////            ->select([
////                     'model_id',
////                 ])
////            ->get();
////        dd($_response);
//
//
//        return $_response;
//    }

}
