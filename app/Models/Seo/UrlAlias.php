<?php

    namespace App\Models\Seo;

    use App\Library\BaseModel;

    class UrlAlias extends BaseModel
    {

        protected $table = 'url_alias';
        protected $guarded = [];
        protected $entity;
        protected $founder;

        public function __construct($entity = NULL, $founder = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
            $this->founder = $founder;
        }

        public function _items_for_menu($search_string = NULL)
        {
            $_response = [];
            if ($search_string) {
                $_items = self::from('url_alias as a')
                    ->with([
                        'model'
                    ])
                    ->where('a.model_default_title', 'like', "%{$search_string}%")
                    ->limit(8)
                    ->get([
                        'a.*',
                    ]);
                if ($_items->count()) {
                    $_items->each(function ($_item) use (&$_response) {
                        if ($_model = $_item->model) {
                            $_item_row = [
                                'name' => "{$_model->id}::{$_model->title}",
                                'view' => NULL,
                                'data' => $_item->id
                            ];
                            $_related_model_class_basename = class_basename($_model->getMorphClass());
                            switch ($_related_model_class_basename) {
                                case 'Node':
                                    $_item_row['view'] = $_model->_page->title;
                                    break;
                                case 'Page':
                                    $_item_row['view'] = $_model->_types($_model->type);
                                    break;
                                case 'Tag':
                                    $_item_row['view'] = 'Страница тега';
                                    break;
                                case 'Brand':
                                    $_item_row['view'] = 'Страница брэнда';
                                    break;
                                case 'Category':
                                    $_item_row['view'] = 'Категория магазина';
                                    break;
                                case 'Product':
                                    $_item_row['view'] = 'Товар магазина';
                                    break;
                            }
                            $_response[] = $_item_row;
                        }
                    });
                }
            }

            return $_response;
        }

        public function set()
        {
            $_response = NULL;
            $_url = request()->input('url');
            if ($this->entity && $_url) {
                $_re_render = (int)($_url['re_render'] ?? 0);
                $_sitemap = (int)($_url['sitemap'] ?? 0);
                $_locale = request()->input('locale', config('app.locale'));
                $_changefreq = request()->input('url.changefreq', 'monthly');
                $_priority = request()->input('url.priority', 0.5);
                $_request_alias = isset($_url['alias']) && $_url['alias'] && !$_re_render ? $_url['alias'] : NULL;
                $_generate_alias = NULL;
                if ($this->entity->_alias->id) {
                    $_url_alias = $this->entity->_alias;
                    if ($_request_alias && ($_request_alias != $_url_alias->alias)) {
                        $_generate_alias = generate_alias($_request_alias);
                    } elseif (!$_request_alias) {
                        $_generate_alias = generate_alias($this->entity->title, $this->founder);
                    } elseif ($_request_alias && (self::where('alias', $_request_alias)->count() > 1)) {
                        $_generate_alias = $_request_alias;
                    }
                    if ($_generate_alias) {
                        if (self::where('alias', $_generate_alias)
                                ->where('id', '<>', $this->entity->_alias->id)
                                ->count() > 0
                        ) {
                            $index = 0;
                            while ($index <= 100) {
                                $_generate_url = "{$_generate_alias}-{$index}";
                                if (self::where('alias', $_generate_url)
                                        ->where('id', '<>', $this->entity->alias_id)
                                        ->count() == 0
                                ) {
                                    $_generate_alias = $_generate_url;
                                    break;
                                }
                                $index++;
                            }
                        }
                        $_url_alias->update([
                            'alias'               => $_generate_alias,
                            'sitemap'             => $_sitemap,
                            'changefreq'          => $_changefreq,
                            'priority'            => $_priority,
                            'model_default_title' => $this->entity->getTranslation('title', $_locale),
                        ]);
                    } else {
                        $_url_alias->update([
                            'sitemap'             => $_sitemap,
                            'changefreq'          => $_changefreq,
                            'priority'            => $_priority,
                            'model_default_title' => $this->entity->getTranslation('title', $_locale),
                        ]);
                    }
                } else {
                    $_url_alias = is_null($_request_alias) ? $this->entity->title : $_request_alias;
                    $_generate_alias = generate_alias($_url_alias, $this->founder);
                    if ($this->where('alias', $_generate_alias)
                            ->count() > 0
                    ) {
                        $index = 0;
                        while ($index <= 100) {
                            $_generate_url = "{$_generate_alias}-{$index}";
                            if (self::where('alias', $_generate_url)
                                    ->count() == 0
                            ) {
                                $_generate_alias = $_generate_url;
                                break;
                            }
                            $index++;
                        }
                    }
                    $_save = new self();
                    $_save->fill([
                        'alias'               => $_generate_alias,
                        'sitemap'             => $_sitemap,
                        'changefreq'          => $_changefreq,
                        'priority'            => $_priority,
                        'model_default_title' => $this->entity->getTranslation('title', $_locale),
                    ]);
                    $_url_alias = $this->entity->_alias()->save($_save);
                }
                $_response = $_url_alias;
            }

            return $_response;
        }

        public function model()
        {
            return $this->morphTo()
                ->with([
                    '_alias'    => function ($q) {
                        $q->remember(REMEMBER_LIFETIME);
                    },
//                    '_comments' => function ($q) {
//                        $q->remember(REMEMBER_LIFETIME);
//                    }
                ]);
        }

        public function _redirect()
        {
            return $this->hasOne(Redirect::class, 'alias_id')
                ->withDefault();
        }

    }
