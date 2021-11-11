<?php

    namespace App\Http\Controllers\Dashboard\Shop;

    use App\Imports\ProductPrices;
    use App\Imports\Products;
    use App\Library\BaseController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Maatwebsite\Excel\Facades\Excel;
    use Matrix\Exception;

    class ImportController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:shop_products_update'
            ]);
            $this->permissions = [
                'read'   => 'shop_products_read',
                'create' => 'shop_products_create',
                'update' => 'shop_products_update',
                'delete' => 'shop_products_delete'
            ];
        }

        protected function _form_import($entity)
        {
            $_form = $this->__form();
            $_form->theme = 'backend.forms.form_empty';
            $_form->route_tag = _r('oleus.shop_import.run');
            $_form->permission['apply'] = 'shop_products_update';
            $_form->contents = [
                field_render('file', [
                    'type'       => 'file',
                    'label'      => 'Файл импорта',
                    'ajax_url'   => FALSE,
                    'attributes' => [
                        'placeholder' => 'Выбрать файл...'
                    ],
                    'help'       => 'Файлы загружаемые для импорта должны иметь расширение <strong>xlsx</strong>, <strong>xls</strong>'
                ])
            ];

            return $_form;
        }

        protected function _form_load($entity)
        {
            $_form = $this->__form();
            $_form->theme = 'backend.forms.form_empty';
            $_form->route_tag = _r('oleus.shop_load_price.run');
            $_form->permission['apply'] = 'shop_products_update';
            $_form->contents = [
                field_render('file', [
                    'type'       => 'file',
                    'label'      => 'Файл цен и наличия',
                    'ajax_url'   => FALSE,
                    'attributes' => [
                        'placeholder' => 'Выбрать файл...'
                    ],
                    'help'       => 'Файлы загружаемые для обновление цен должны иметь расширение <strong>xlsx</strong>, <strong>xls</strong>'
                ])
            ];

            return $_form;
        }

        public function import(Request $request)
        {
            $_item = $this->entity;
            $_wrap = $this->render([
                'seo.title' => 'Импорт товаров'
            ]);
            $_form = $this->_form_import($_item);

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }

        public function load(Request $request)
        {
            $_item = $this->entity;
            $_wrap = $this->render([
                'seo.title' => 'Загрузка цен товаров'
            ]);
            $_form = $this->_form_load($_item);

            return view($_form->theme, compact('_form', '_item', '_wrap'));
        }

        public function import_run(Request $request)
        {
            $_file = $request->file('file');
            $_validator_request['file'] = $_file;
            $_validator_rules['file'] = 'required';
            if ($_file) {
                $_validator_request['extension'] = strtolower($_file->getClientOriginalExtension());
                $_validator_rules['extension'] = 'required|in:xlsx,xls';
            }
            $_validator = Validator::make($_validator_request, $_validator_rules, [], [
                'file'      => 'Файл импорта',
                'extension' => 'Расширение файла'
            ]);
            if ($_validator->fails()) {
                return back()
                    ->withErrors($_validator);
            }
            try {
                $_file_path = $request->file('file')->store('tmp');
                $_file_path = storage_path("app/{$_file_path}");
                Excel::import(new Products, $_file_path);
            } catch (Exception $exception) {
                spy('Возникла ошибка при попытке импортировать товары на сайт. Ошибка записана в <span class="uk-text-bold">LOG файл</span>', 'error');

                return redirect()
                    ->back()
                    ->with('notice', [
                        [
                            'message' => 'Файл импорта товаров не удалось загрузить!',
                            'status'  => 'danger'
                        ]
                    ]);
            }

            return redirect()
                ->back()
                ->with('notices', [
                    [
                        'message' => 'Файл импорта товаров успешно загружен!',
                        'status'  => 'success'
                    ],
                    [
                        'message' => 'Товары добавлены в очередь задач на сохранение.',
                        'status'  => 'success'
                    ]
                ]);
        }

        public function load_run(Request $request)
        {
            $_file = $request->file('file');
            $_validator_request['file'] = $_file;
            $_validator_rules['file'] = 'required';
            if ($_file) {
                $_validator_request['extension'] = strtolower($_file->getClientOriginalExtension());
                $_validator_rules['extension'] = 'required|in:xlsx,xls';
            }
            $_validator = Validator::make($_validator_request, $_validator_rules, [], [
                'file'      => 'Файл импорта',
                'extension' => 'Расширение файла'
            ]);
            if ($_validator->fails()) {
                return back()
                    ->withErrors($_validator);
            }
            try {
                $_file_path = $request->file('file')->store('tmp');
                $_file_path = storage_path("app/{$_file_path}");
                Excel::import(new ProductPrices(), $_file_path);
            } catch (Exception $exception) {
                spy('Возникла ошибка при попытке загрузки цен товаров на сайт. Ошибка записана в <span class="uk-text-bold">LOG файл</span>', 'error');

                return redirect()
                    ->back()
                    ->with('notice', [
                        [
                            'message' => 'Файл загрузки цен товаров не удалось загрузить!',
                            'status'  => 'danger'
                        ]
                    ]);
            }

            return redirect()
                ->back()
                ->with('notices', [
                    [
                        'message' => 'Файл загрузки цен товаров успешно загружен!',
                        'status'  => 'success'
                    ],
                    [
                        'message' => 'Данные добавлены в очередь задач на сохранение.',
                        'status'  => 'success'
                    ]
                ]);
        }

    }
