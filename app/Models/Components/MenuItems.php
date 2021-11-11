<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;
    use App\Models\Seo\UrlAlias;

    class MenuItems extends BaseModel
    {

        const DEFAULT_ITEMS = [
            '<front>',
            '<none>'
        ];
        protected $table = 'menu_items';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'title',
            'sub_title',
        ];

        public function _get_alias()
        {
            if (!is_null($this->alias_id)) {
                $_url_alias = UrlAlias::find($this->alias_id);
                if ($_url_alias) {
                    if ($_related_model = $_url_alias->model) {
                        return (object)[
                            'id'    => $_url_alias->id,
                            'name'  => "{$_related_model->id}::{$_related_model->title}",
                            'alias' => $_url_alias->alias
                        ];
                    }
                }
            } elseif ($this->link) {
                return (object)[
                    'id'    => $this->link,
                    'name'  => $this->link,
                    'alias' => NULL
                ];
            }

            return NULL;
        }

        public function _children()
        {
            return $this->hasMany(self::class, 'parent_id', 'id')
                ->with([
                    '_children',
                    '_alias'
                ])
                ->orderBy('sort');
        }

        public function _sub_items()
        {
            $items = self::where('parent_id', $this->id)
                ->get();

            return $items ?? NULL;
        }

        public function _alias()
        {
            return $this->hasOne(UrlAlias::class, 'id', 'alias_id')
                ->withDefault()
                ->remember(REMEMBER_LIFETIME);
        }

    }
