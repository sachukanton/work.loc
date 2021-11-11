<?php

    namespace App\Http\Controllers\Dashboard;

    use App\Library\BaseController;
    use App\Models\File\File;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;

    class FileController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function upload(Request $request)
        {
            if ($request->hasFile('file')) {
                $_file = $request->file('file');
                $_file_size = $_file->getClientSize();
                $_file_base_name = $_file->getClientOriginalName();
                if (!$_item = File::where('base_name', $_file_base_name)
                    ->where('filesize', $_file_size)
                    ->first()) {
                    $_file_mime_type = $_file->getClientMimeType();
                    $_file_extension = $_file->getClientOriginalExtension();
                    $_file_name = str_slug(basename($_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                    Storage::disk('public')
                        ->put($_file_name, file_get_contents($_file->getRealPath()));
                    $_item = File::updateOrCreate([
                        'id' => NULL
                    ], [
                        'base_name' => $_file_base_name,
                        'filename'  => $_file_name,
                        'filemime'  => $_file_mime_type,
                        'filesize'  => $_file_size,
                    ]);
                } else {
                    $_item = $_item->replicate();
                    $_item->save();
                }
                $_options = $request->only([
                    'field',
                    'view'
                ]);
                $_file_render = clear_html(preview_file_render($_item, $_options));

                return response($_file_render, 200);
            } else {
                return response(trans('notice.field_upload_not_upload'), 422);
            }
        }

        public function update(Request $request, File $file)
        {
            if ($request->has('form')) {
                $file->update($request->get('file'));
                $commands['commands'][] = [
                    'command' => 'UK_notification',
                    'options' => [
                        'text'   => 'Элемент сохранен',
                        'status' => 'success',
                    ]
                ];
                $commands['commands'][] = [
                    'command' => 'UK_modalClose',
                    'options' => []
                ];
            } else {
                $_form = (object)[
                    'class'  => 'uk-form-stacked',
                    'action' => _r('ajax.file.update', ['file' => $file->id]),
                    'method' => 'POST',
                    'fields' => [
                        '<h2 class="uk-heading-divider uk-text-color-teal">Дополнительные параметры</h2>',
                        field_render('form', [
                            'type'  => 'hidden',
                            'value' => 1,
                        ]),
                        field_render('file.title', [
                            'label'      => 'Атрибут TITLE',
                            'type'       => 'textarea',
                            'value'      => $file->title,
                            'attributes' => [
                                'rows' => 3,
                            ]
                        ]),
                        field_render('file.alt', [
                            'label'      => 'Атрибут ALT',
                            'type'       => 'textarea',
                            'value'      => $file->alt,
                            'attributes' => [
                                'rows' => 3,
                            ]
                        ]),
                        field_render('file.description', [
                            'label'  => 'Описание файла',
                            'type'   => 'textarea',
                            'value'  => $file->description,
                            'editor' => TRUE,
                            'class'  => 'editor-short',
                        ]),
                        field_render('file.sort', [
                            'type'  => 'number',
                            'label' => 'Порядок сортировки',
                            'value' => $file->sort ?? 0,
                        ]),
                    ],
                    'button' => [
                        'text' => 'Сохранить'
                    ],
                    'prefix' => '<div class="uk-modal-body">',
                    'suffix' => '</div>',
                ];
                $commands['commands'][] = [
                    'command' => 'UK_modal',
                    'options' => [
                        'content' => '<button class="uk-modal-close-default" type="button" uk-close></button>' . view('backend.forms.form_default', compact('_form'))
                                ->render()
                    ]
                ];
            }

            return response($commands, 200);
        }

        //        public function remove(Request $request)
        //        {
        //            if ($_fid = $request->input('fid')) {
        //                f_delete($_fid);
        //
        //                return response(trans('notice.field_upload_deleted'), 200);
        //            } else {
        //                return response(trans('notice.field_upload_not_deleted'), 422);
        //            }
        //        }

        public function get(Request $request, $preset, $file_name = NULL)
        {
            $_fileName = $file_name ? : $preset;

            dd($file_name);
        }

    }
