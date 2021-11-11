<?php

    namespace App\Library;

    use App\Http\Controllers\Controller;
    use Illuminate\Foundation\Auth\Access\Authorizable;

    abstract class BaseController extends Controller
    {

        use Authorizable,
            Dashboards;

        protected $entity;
        public $tags;
        public $defaultLocale = 'en';

        public function __construct()
        {
            parent::__construct();
            $this->defaultLocale = config('app.locale');
        }

        public function __call($name, $arguments)
        {
            switch ($name) {
                case 'index':
                    if ($this->__can_permission() == FALSE) abort(403);
                    $_wrap = $this->render([
                        'seo.title' => $this->titles['index']
                    ]);

                    return $this->_items($_wrap);
                    break;
                case 'create':
                    if ($this->__can_permission('create') == FALSE) abort(403);
                    $_item = $this->entity;
                    $_wrap = $this->render([
                        'seo.title' => $this->titles['create']
                    ]);
                    $_form = $this->_form($_item);

                    return view($_form->theme, compact('_form', '_item', '_wrap'));
                    break;
                case 'edit':
                    try {
                        $_item = array_shift($arguments);
                        if ($this->__can_permission('edit') == FALSE) abort(403);
                        $_wrap = $this->render([
                            'seo.title' => str_replace([
                                ':id',
                                ':title',
                                ':question',
                                ':name'
                            ], [
                                strip_tags(str_limit("#{$_item->id}", 30)),
                                strip_tags(str_limit($_item->title, 30)),
                                strip_tags(str_limit($_item->question, 30)),
                                strip_tags(str_limit($_item->display_name ?? ($_item->full_name ?? $_item->name)))
                            ], $this->titles['edit'])
                        ]);
                        $_form = $this->_form($_item);

                        return view($_form->theme, compact('_form', '_item', '_wrap'));
                    } catch (\Exception $exception) {

                        throw $exception;
                    }
                    break;
                case 'translate':
                    try {
                        if ($this->__can_permission('edit') == FALSE) abort(403);
                        $_item = array_shift($arguments);
                        $locale = array_shift($arguments);
                        $_locale = config("laravellocalization.supportedLocales.{$locale}");
                        if (is_null($_locale)) abort(404);
                        $_wrap = $this->render([
                            'seo.title' => str_replace(':locale', $_locale['native'], $this->titles['translate'])
                        ]);
                        $_form = $this->_form_translate($_item, $locale);

                        return view($_form->theme, compact('_form', '_item', '_wrap'));
                    } catch (\Exception $exception) {
                        throw $exception;
                    }
                    break;
                case 'destroy':
                    try {
                        $_item = array_shift($arguments);
                        if ($this->__can_permission('delete') == FALSE) abort(403);
                        $_item->delete();

                        return $this->__response_after_destroy(request(), $_item);
                    } catch (\Exception $exception) {
                        throw $exception;
                    }
                    break;
            }
        }

        protected function _items($wrap)
        {
        }

        protected function _form($entity)
        {
        }

        protected function _form_translate($entity, $locale)
        {
        }

    }
