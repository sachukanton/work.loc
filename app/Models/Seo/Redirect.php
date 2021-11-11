<?php

    namespace App\Models\Seo;

    use App\Library\BaseModel;

    class Redirect extends BaseModel
    {

        protected $table = 'redirects';
        protected $guarded = [];
        protected $entity;
        public $timestamps = FALSE;

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

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

        public function _alias()
        {
            return $this->belongsTo(UrlAlias::class, 'alias_id', 'id')
                ->with([
                    'model'
                ])
                ->withDefault();
        }

    }
