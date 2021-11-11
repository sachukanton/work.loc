<?php

    namespace App\Models\Seo;

    use App\Library\BaseModel;

    class TmpMetaTags extends BaseModel
    {

        protected $table = 'tmp_meta_tags';
        protected $guarded = [];
        protected $entity;
        public $timestamps = FALSE;
        public $translatable = [
            'meta_title',
            'meta_description',
            'meta_keywords',
        ];

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

        public function related_model()
        {
            return $this->hasOne($this->model_type, 'id', 'model_id')
                ->with([
                    '_alias',
                    '_relation_entity'
                ]);
        }

        public function set()
        {
            $_tmp_meta_tags = request()->get('tmp_meta_tags');
            if ($this->entity && $_tmp_meta_tags) {
                if ($this->entity->_tmp_meta_tags instanceof TmpMetaTags) {
                    $_locale = request()->get('locale', config('app.default_locale'));
                    foreach ($_tmp_meta_tags as $_field => $_value) {
                        $this->entity->_tmp_meta_tags->setTranslation($_field, $_locale, $_value);
                    }
                    $this->entity->_tmp_meta_tags->save();
                } else {
                    $_locale = request()->get('locale', config('app.default_locale'));
                    foreach ($_tmp_meta_tags as $_type => $_value) {
                        if ($this->entity->_tmp_meta_tags->isNotEmpty()) {
                            $_save = $this->entity->_tmp_meta_tags->where('type', $_type)
                                ->first();
                            $_save->setTranslation('meta_title', $_locale, $_value['meta_title']);
                            $_save->setTranslation('meta_keywords', $_locale, $_value['meta_keywords']);
                            $_save->setTranslation('meta_description', $_locale, $_value['meta_description']);
                            $_save->save();
                        } else {
                            $_save = new self();
                            $_save->type = $_type;
                            $_save->setTranslation('meta_title', $_locale, $_value['meta_title']);
                            $_save->setTranslation('meta_keywords', $_locale, $_value['meta_keywords']);
                            $_save->setTranslation('meta_description', $_locale, $_value['meta_description']);
                            $this->entity->_tmp_meta_tags()->save($_save);
                        }
                    }
                }
            }
        }

        public function model()
        {
            return $this->morphTo();
        }

    }
