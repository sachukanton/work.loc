<?php

namespace App\Library;

use App\Models\Components\Comment;
use App\Models\Components\DisplayRules;
use App\Models\File\File;
use App\Models\File\FilesReference;
use App\Models\Seo\SearchIndex;
use App\Models\Seo\TmpMetaTags;
use App\Models\Seo\UrlAlias;
use App\Models\Structure\Tag;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Translatable\HasTranslations;
use Watson\Rememberable\Rememberable;

abstract class BaseModel extends Model
{

    use Rememberable;
    use HasTranslations;

    protected $perPage = 50;
    public $defaultLocale = 'en';
    public $frontLocale;
    public $dashboard;
    public $device = 'pc';
    public $deviceTemplate = 'default';
    public $wrap;
    public $template;
    public $viewAccessChecked;
    public $invisible = FALSE;
    public $invisiblePrefix = '<span class="invisible-entity">';
    public $invisibleSuffix = '</span>';
    public $relatedMedias;
    public $relatedFiles;
    public $comments;

    public function __construct()
    {
        global $wrap;
        //            $this->wrap = $wrap;
        $this->dashboard = $wrap['dashboard'] ?? FALSE;
        $this->frontLocale = $wrap['locale'] ?? DEFAULT_LOCALE;
        $this->device = $wrap['device'] ?? 'pc';
        $this->defaultLocale = config('app.locale');
        if ($this->device == 'mobile' && !$this->dashboard) $this->deviceTemplate = 'mobile';
    }

    /**
     * Scope
     */
    public function scopeActive($query, $status = 1)
    {
        return $query->where('status', $status);
    }

    public function scopeVisibleOnList($query, $status = 1)
    {
        return $query->where('visible_on_list', $status);
    }

    public function scopeVisibleOnBlock($query, $status = 1)
    {
        return $query->where('visible_on_block', $status);
    }

    public function scopeBlocked($query, $blocked = 1)
    {
        return $query->where('blocked', $blocked);
    }

    public function scopeUsed($query, $used = 1)
    {
        return $query->where('used', $used);
    }

    public function scopeTags()
    {
        return Tag::all();
    }

    /**
     * Attribute
     */
    public function hasAttribute($attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }

    public function getLastModifiedAttribute()
    {
        if (isset($this->updated_at)) return $this->updated_at;

        return Carbon::parse(config('os_seo.last_modified_timestamp'));
    }

    public function getGenerateUrlAttribute()
    {
        if ($this->_alias->id) {
            return _u(LaravelLocalization::getLocalizedURL($this->frontLocale, $this->_alias->alias));
        } else {
            return _u(LaravelLocalization::getLocalizedURL($this->frontLocale, '/'));
        }
    }

    public function getVisibleEntityAttribute()
    {
        global $wrap;
        $_display_rules = $this->_display_rules;
        if ($_display_rules->isNotEmpty()) {
            $_visible = $_display_rules->pluck('rule')->flip()->map(function ($_rule) {
                return FALSE;
            });
            foreach ($_display_rules->groupBy('rule') as $_group => $_rules) {
                $_values = NULL;
                switch ($_group) {
                    case 'user_roles':
                        $_role = $wrap['user_role'];
                        $_values = $_rules->first(function ($_item) use ($_role) {
                            return $_role == $_item->value;
                        });
                        break;
                    case 'languages':
                        $_locale = $wrap['locale'];
                        $_values = $_rules->first(function ($_item) use ($_locale) {
                            return $_locale == $_item->value;
                        });
                        break;
                    case 'locations':

                        break;
                    case 'pages':
                        $_url = $wrap['seo']['url_alias'] ?? NULL;
                        $_values = $_rules->first(function ($_item) use ($_url) {
                            if ($_url) return str_is($_item->value, $_url);
                            if (!$_url && $_item->value == '<front>') return TRUE;
                        });
                        break;
                }
                if ($_values) $_visible[$_group] = TRUE;
            }
            $_state = $_visible->first(function ($_value) {
                return $_value == FALSE;
            });

            return is_null($_state) ? TRUE : FALSE;
        }

        return TRUE;
    }

    public function getViewAccessAttribute()
    {
        if (is_null($this->view_access_checked)) {
            global $wrap;
            $this->view_access_checked = FALSE;
            if ($this->status && $this->visible_entity) {
                $this->view_access_checked = TRUE;
            } elseif ($this->status && isset($wrap['user_role']) && $wrap['user_role'] == 'super_admin') {
                // todo: Если убрать из условия $this->status, то будут выводиться и не опубликованные элементы, а в подменю будет написано "Не опубликовано"
                $this->view_access_checked = !$this->status ? 'not-published' : 'limited-visibility';
            }

            return $this->view_access_checked;
        }

        return $this->view_access_checked;
    }

    public function getCommentRatesAttribute()
    {
        $_response = [
            'quantity' => 0,
            'rate'     => 0,
            'percent'  => 0,
            'rates'    => [
                1 => [
                    'quantity' => 0,
                    'rate'     => 0,
                    'percent'  => 0
                ],
                2 => [
                    'quantity' => 0,
                    'rate'     => 0,
                    'percent'  => 0
                ],
                3 => [
                    'quantity' => 0,
                    'rate'     => 0,
                    'percent'  => 0
                ],
                4 => [
                    'quantity' => 0,
                    'rate'     => 0,
                    'percent'  => 0
                ],
                5 => [
                    'quantity' => 0,
                    'rate'     => 0,
                    'percent'  => 0
                ],
            ],
            'markup'   => [
                'comment' => '<div><a href="' . $this->generate_url . '?tab=comments" class="uk-link-color-blue-grey"><span class="uk-margin-small-right" uk-icon="icon: comment"></span>' . trans('forms.buttons.comment.add_first_comment') . '</a></div>',
                'review'  => '<div><a href="' . $this->generate_url . '?tab=comments" class="uk-link-color-blue-grey"><span class="uk-margin-small-right" uk-icon="icon: comment"></span>' . trans('forms.buttons.comment.add_first_review') . '</a></div>',
            ]
        ];
        $_comments = $this->_comments()
            ->where('status', 1)
            ->select(DB::raw('count(*) as count, rate'))
            ->groupBy('rate')
            ->get()
            ->keyBy('rate')
            ->sortKeysDesc();
        if ($_comments->isNotEmpty()) {
            $_comments->each(function ($_data, $_rate) use (&$_response) {
                $_response['quantity'] += $_data->count;
                $_response['rate'] = $_response['rate'] + $_data->count * $_rate;
                $_response['rates'][$_rate]['quantity'] = $_data->count;
                $_response['rates'][$_rate]['rate'] = $_data->count * $_rate;
            });
        }
        if ($_response['quantity']) {
            foreach ($_response['rates'] as &$_rate) if ($_rate['quantity']) if ($_response['rate']) $_rate['percent'] = round($_rate['rate'] * 100 / $_response['rate'], 1, PHP_ROUND_HALF_DOWN);
            $_response['rate'] = round($_response['rate'] / $_response['quantity'], 1, PHP_ROUND_HALF_DOWN);
            $_response['percent'] = round($_response['rate'] * 100 / 5, 1, PHP_ROUND_HALF_DOWN);
            $_response['markup'] = '<div class="uk-flex uk-flex-middle"><div class="uk-position-relative"><div><span uk-icon="icon: stargrade" class="uk-text-color-grey lighten-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-grey lighten-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-grey lighten-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-grey lighten-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-grey lighten-2"></span></div><div class="uk-position-top-left uk-text-nowrap uk-overflow-hidden" style="width: ' . $_response['percent'] . '%"><span uk-icon="icon: stargrade" class="uk-text-color-amber darken-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-amber darken-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-amber darken-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-amber darken-2"></span><span uk-icon="icon: stargrade" class="uk-text-color-amber darken-2"></span></div></div><div class="uk-margin-small-left uk-badge uk-background-color-grey lighten-1">' . $_response['quantity'] . '</div></div>';
            $_response['markup'] = '<a href="' . $this->generate_url . '?tab=comments" class="uk-link-decoration-none">' . $_response['markup'] . '</a>';
        }

        return $_response;
    }

    public function getSchemaAttribute()
    {
        return NULL;
    }

    public function hasProperty($property)
    {
        return property_exists($this, $property);
    }

    /**
     * Relationships
     */
    public function _alias()
    {
        return $this->morphOne(UrlAlias::class, 'model')
            ->withDefault();
    }

    public function _tags()
    {
        return $this->morphToMany(Tag::class, 'model', 'taggables');
    }

    public function _display_rules()
    {
        return $this->morphMany(DisplayRules::class, 'model');
    }

    public function _tmp_meta_tags()
    {
        return $this->morphOne(TmpMetaTags::class, 'model')
            ->withDefault();
    }

    public function _background()
    {
        return $this->hasOne(File::class, 'id', 'background_fid');
    }

    public function _preview()
    {
        return $this->hasOne(File::class, 'id', 'preview_fid');
    }

    public function _preview_full()
    {
        return $this->hasOne(File::class, 'id', 'full_fid');
    }

    public function _preview_mobile()
    {
        return $this->hasOne(File::class, 'id', 'mobile_fid');
    }

    public function _video_preview()
    {
        return $this->hasOne(File::class, 'id', 'video_preview_fid');
    }

    public function _video()
    {
        return $this->hasOne(File::class, 'id', 'video_fid');
    }

    public function _avatar()
    {
        return $this->hasOne(File::class, 'id', 'avatar_fid');
    }

    public function _icon()
    {
        return $this->hasOne(File::class, 'id', 'icon_fid');
    }

    public function _user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->withDefault();
    }

    public function _files_related()
    {
        return $this->morphToMany(File::class, 'model', 'files_related');
    }

    //        public function _comments()
    //        {
    //            return $this->morphMany(Comment::class, 'model')
    //                ->whereNull('reply')
    //                ->orderByDesc('created_at');
    //        }

    public function _search_index()
    {
        return $this->morphMany(SearchIndex::class, 'model');
    }

    public function _file()
    {
        return $this->hasOne(File::class, 'id', 'file_fid')
            ->remember(60)
            ->withDefault();
    }

    /**
     * Others
     */
    public function setWrap($variables = NULL)
    {
        if (is_array($variables) && $variables) foreach ($variables as $_variable => $_data) if ($_data) wrap()->set($_variable, $_data);
    }

    public function getComments($options = [])
    {
        $_user = Auth::user();
        $_options = array_merge([
            'page' => 1,
            'rate' => 'all'
        ], $options);
        Paginator::currentPageResolver(function () use ($_options) {
            return $_options['page'];
        });
        if ($_user && $_user->can('comments_update')) {
            $_items = $this->_comments()
                ->with([
                    '_reply',
                    '_user'
                ])
                ->when($_options['rate'] != 'all', function ($query) use ($_options) {
                    return $query->where('rate', $_options['rate']);
                })
                ->paginate();
        } else {
            $_items = $this->_comments()
                ->with([
                    '_reply',
                    '_user'
                ])
                ->when($_options['rate'] != 'all', function ($query) use ($_options) {
                    return $query->where('rate', $_options['rate']);
                })
                ->active()
                ->paginate();
        }

        return $_items;
    }

    public function _background_asset($preset = NULL, $options = [])
    {
        if ($this->exists && $this->background_fid && ($_background = $this->_background)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE
            ], $options);

            return image_render($_background, $preset, $_options);
        }

        return NULL;
    }

    public function _background_style($preset = NULL, $options = [])
    {
        if ($this->exists && $this->background_fid && ($_background_style = $this->_background_asset($preset, $options))) {
            return "background-image: url('{$_background_style}')";
        }

        return NULL;
    }

    public function _preview_asset($preset = NULL, $options = [])
    {
        if ($this->exists && $this->preview_fid && ($_preview = $this->_preview)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE,
                'attributes'     => []
            ], $options);

            return image_render($_preview, $preset, $_options);
        }

        return NULL;
    }

    public function _preview_asset_full($preset = NULL, $options = [])
    {
        if ($this->exists && $this->full_fid && ($_preview = $this->_preview)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE,
                'attributes'     => []
            ], $options);

            return image_render($_preview, $preset, $_options);
        }

        return NULL;
    }

    public function _preview_asset_mobile($preset = NULL, $options = [])
    {
        if ($this->exists && $this->mobile_fid && ($_preview = $this->_preview)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE,
                'attributes'     => []
            ], $options);

            return image_render($_preview, $preset, $_options);
        }

        return NULL;
    }

    public function _avatar_asset($preset = NULL, $options = [])
    {
        if ($this->exists && $this->avatar_fid && ($_avatar = $this->_avatar)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE,
                'attributes'     => []
            ], $options);

            return image_render($_avatar, $preset, $_options);
        }

        return NULL;
    }

    public function _icon_asset($preset = NULL, $options = [])
    {
        if ($this->exists && $this->icon_fid && ($_icon = $this->_icon)) {
            $_options = array_merge([
                'no_last_modify' => FALSE,
                'only_way'       => TRUE,
                'attributes'     => []
            ], $options);

            return image_render($_icon, $preset, $_options);
        }

        return NULL;
    }

    public static function tree_parents($id = NULL)
    {
        $_response = collect([]);
        if ($id) {
            $_categories = self::where('id', '<>', $id)
                ->orderBy('parent_id')
                ->orderBy('title')
                ->get([
                    'id',
                    'title',
                    'parent_id'
                ])
                ->keyBy('id');
        } else {
            $_categories = self::orderBy('parent_id')
                ->orderBy('title')
                ->get([
                    'id',
                    'title',
                    'parent_id'
                ])
                ->keyBy('id');
        }
        if ($_categories->isNotEmpty()) {
            $_response = collect([]);
            $_categories->each(function ($_item) use (&$_response, $_categories) {
                if ($_item->parent_id) return FALSE;
                $_data = [
                    'id'         => $_item->id,
                    'parents'    => [],
                    'parents_id' => [],
                    'title'      => $_item->getTranslation('title', config('app.locale')),
                    'entity'     => $_item
                ];
                $_response->put($_item->id, $_data);
                self::tree_parents_item($_response, $_categories, $_data);
            });
            $_response = $_response->map(function ($_item) {
                $_item['title_parent'] = $_item['parents'] ? implode($_item['parents'], ' / ') : NULL;
                $_item['title_option'] = $_item['title_parent'] ? "{$_item['title_parent']} / {$_item['title']}" : $_item['title'];

                return $_item;
            });

        }

        return $_response;
    }

    public static function tree_parents_item(&$_response, $categories, $parent = NULL)
    {
        $categories->each(function ($_item) use (&$_response, $categories, $parent) {
            if ($_item->parent_id == $parent['id']) {
                $_data = [
                    'id'         => $_item->id,
                    'parents'    => array_merge($parent['parents'], [
                        $parent['id'] => $parent['title']
                    ]),
                    'parents_id' => array_merge($parent['parents_id'], [$parent['id']]),
                    'title'      => $_item->getTranslation('title', config('app.locale'))
                ];
                $_response->put($_item->id, $_data);
                self::tree_parents_item($_response, $categories, $_data);
            }
        });
    }

    public function getShortcut($options = [])
    {
        return NULL;
    }

}
