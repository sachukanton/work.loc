<?php

    use App\Library\Fields;
    use App\Models\Components\Advantage;
    use App\Models\Components\Banner;
    use App\Models\Components\Block;
    use App\Models\Components\Menu;
    use App\Models\Components\Slider;
    use App\Models\Form\Forms;
    use App\Models\Structure\Faq;
    use App\Models\Structure\Page;
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\View;
    use Intervention\Image\Facades\Image;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
    use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

    if (!function_exists('render_attributes')) {
        function render_attributes($attributes = [])
        {
            if (!is_array($attributes) || !count($attributes)) return NULL;
            $_attributes = NULL;
            foreach ($attributes as $key => $attribute) {
                if (is_string($key) && is_array($attribute) && count($attribute)) {
                    $_array_attribute = [];
                    foreach ($attribute as $_data) if ($_data) $_array_attribute[] = $_data;
                    $_array_attribute = count($_array_attribute) ? implode(' ', $_array_attribute) : NULL;
                    $_attributes[] = "{$key}=\"{$_array_attribute}\"";
                } elseif (is_string($key) && !is_null($attribute) && !is_bool($attribute) && (is_string($attribute) || is_numeric($attribute) || is_float($attribute))) {
                    $_attributes[] = "{$key}=\"{$attribute}\"";
                } elseif (is_string($key) && (is_null($attribute) || (is_bool($attribute) && $attribute == TRUE))) {
                    $_attributes[] = $key;
                } elseif (!is_null($attribute) && !is_bool($attribute)) {
                    if ($attribute) $_attributes[] = $attribute;
                }
            }

            return $_attributes ? implode(' ', $_attributes) : NULL;
        }
    }

    if (!function_exists('image_render')) {
        function image_render($file = NULL, $preset = NULL, $options = [])
        {
            $_default = [
                'outside_file'   => NULL,
                'no_last_modify' => FALSE,
                'only_way'       => FALSE,
                'attributes'     => [
                    'title' => $file->title ?? NULL,
                    'alt'   => $file->alt ?? NULL,
                ]
            ];
            $_options = array_merge_recursive_distinct($_default, $options);
            $_no_image = is_null($file) ? TRUE : FALSE;
            $_presets = collect(config('os_images'));
            $_path = $preset ? public_path("preset/{$preset}") : public_path('images');
            $_file_path = $preset ? "preset/{$preset}" : "images";
            $_file_name = $_no_image ? 'no-image.jpg' : ($file->filename ?? $file);
            $_file_exists = file_exists("{$_path}/{$_file_name}");
            $_file_content = NULL;
            if (!$_file_exists) {
                File::isDirectory($_path) or File::makeDirectory($_path, 0777, TRUE, TRUE);
                if ($_options['outside_file']) {
                    $_outside_file = NULL;
                    $_outside_file_path = $_options['outside_file']['path'];
                    $_outside_file_name = $_options['outside_file']['name'];
                    if (file_exists("{$_path}/{$_outside_file_name}")) {
                        $_outside_file = "{$_file_path}/{$_outside_file_name}";
                    } else {
                        $_preset = $_presets->get($preset);
                        $_quality = isset($_preset['quality']) && is_numeric($_preset['quality']) ? $_preset['quality'] : 100;
                        $_file = Image::make($_outside_file_path);
                        $_w = isset($_preset['w']) && $_preset['w'] ? $_preset['w'] : NULL;
                        $_h = isset($_preset['h']) && $_preset['h'] ? $_preset['h'] : NULL;
                        $_background = isset($_preset['background']) && $_preset['background'] ? $_preset['background'] : NULL;
                        if ($_background) {
                            $_render_image = Image::canvas($_w, $_h, $_background);
                        }
                        if (isset($_preset['fit']) && $_w && $_h) {
                            $_file->fit($_w, $_h);
                        } else {
                            if ($_file->height() < $_h) {
                                $_file->heighten($_h, function ($constraint) {
                                    $constraint->upsize();
                                });
                            }
                            if ($_file->width() < $_w) {
                                $_file->widen($_w, function ($constraint) {
                                    $constraint->upsize();
                                });
                            }
                            $_file->resize($_w, $_h, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                        $_render_image->insert($_file, 'center');
                        if (isset($_preset['watermark']) && is_array($_preset['watermark']) && isset($_preset['watermark']['image']) && $_preset['watermark']['image']) {
                            $_watermark_position = isset($_preset['watermark']['position']) && $_preset['watermark']['position'] ? $_preset['watermark']['position'] : 'center';
                            $_watermark_position_x = $_watermark_position != 'center' ? 15 : NULL;
                            $_watermark_position_y = $_watermark_position != 'center' ? 15 : NULL;
                            $_file->insert(public_path($_preset['watermark']['image']), $_watermark_position, $_watermark_position_x, $_watermark_position_y);
                        }
                        $_outside_file = "{$_file_path}/{$_outside_file_name}";
                        $_render_image->save(public_path($_outside_file), $_quality);
                        ImageOptimizer::optimize(public_path($_outside_file));
                    }
                    $_file_path = formalize_path($_outside_file, $_options['no_last_modify']);
                } else {
                    $_images_mimetype = [
                        'image/jpeg',
                        'image/png',
                        'image/gif'
                    ];
                    $_images_mimetype_no_generate = [
                        'image/x-icon',
                        'image/vnd.microsoft.icon',
                    ];
                    $_images_mimetype_svg = [
                        'image/svg+xml'
                    ];
                    $_file_is_image = (isset($file->filemime) && in_array($file->filemime, $_images_mimetype)) || $_no_image ? TRUE : FALSE;
                    if (is_string($file)) {
                        preg_match('/(.jpg|jpeg|gif|png)$/i', $file, $_file_is_image);
                        $_file_is_image = (boolean)$_file_is_image;
                    }
                    if ($_file_is_image) {
                        if ($preset && $_presets->has($preset)) {
                            $_preset = $_presets->get($preset);
                            $_w = isset($_preset['w']) && $_preset['w'] ? $_preset['w'] : NULL;
                            $_h = isset($_preset['h']) && $_preset['h'] ? $_preset['h'] : NULL;
                            $_background = isset($_preset['background']) && $_preset['background'] ? $_preset['background'] : NULL;
                            $_border = isset($_preset['border']) && $_preset['border'] ? $_preset['border'] : 0;
                            $_render_image = NULL;
                            if ($_background) {
                                $_render_image = Image::canvas($_w, $_h, $_background);
                                $_w -= $_border;
                                $_h -= $_border;
                            }
                            $_quality = isset($_preset['quality']) && is_numeric($_preset['quality']) ? $_preset['quality'] : 100;
                            if ($file instanceof App\Models\File\File) {
                                $_file_path_load = storage_path("app/public/$_file_name");
                                if (!$_file_path_load) return NULL;
                                $_file = Image::make($_file_path_load);
                            } else {
                                $_file_path_load = $_file_name == 'no-image.jpg' ? public_path("images/no-image.jpg") : public_path($_file_name);
                                if (!$_file_path_load) return NULL;
                                $_file = Image::make($_file_path_load);
                            }
                            if (isset($_preset['fit']) && $_w && $_h) {
                                $_file->fit($_w, $_h, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                            } else {
                                //                                if ($_file->height() < $_h) $_file->heighten($_h);
                                //                                if ($_file->width() < $_w) $_file->widen($_w);
                                $_file->resize($_w, $_h, function ($constraint) {
                                    $constraint->aspectRatio();
                                    //                                    $constraint->upsize();
                                });
                                //                                $_file->resizeCanvas($_w, $_h, 'center', TRUE);
                                //                                $_background = isset($_preset['background']) && $_preset['background'] ? $_preset['background'] : NULL;
                                //                                if (isset($_preset['w']) && isset($_preset['h'])) $_file->resizeCanvas($_preset['w'], $_preset['h'], 'center', FALSE, $_background);
                            }
                            if ($_render_image) {
                                $_render_image->insert($_file, 'center');
                            } else {
                                $_render_image = $_file;
                            }
                            if (isset($_preset['watermark']) && is_array($_preset['watermark']) && isset($_preset['watermark']['image']) && $_preset['watermark']['image']) {
                                $_watermark_position = isset($_preset['watermark']['position']) && $_preset['watermark']['position'] ? $_preset['watermark']['position'] : 'center';
                                $_watermark_position_x = $_watermark_position != 'center' ? 15 : NULL;
                                $_watermark_position_y = $_watermark_position != 'center' ? 15 : NULL;
                                $_render_image->insert(public_path($_preset['watermark']['image']), $_watermark_position, $_watermark_position_x, $_watermark_position_y);
                            }
                            if (is_string($file)) {
                                $_file_name = explode('/', $_file_name);
                                $_file_name = array_pop($_file_name);
                            }
                            $_file_path_file = "{$_file_path}/{$_file_name}";
                            $_file_name_webp = explode('.', $_file_name);
                            $_file_path_webp = "{$_file_path}/{$_file_name_webp[0]}.webp";
                            $_render_image->save(public_path($_file_path_file), $_quality);
                            ImageOptimizer::optimize(public_path($_file_path_file));
                            $_render_image->encode('webp')
                                ->save(public_path($_file_path_webp));
                        } else {
                            $_file_path_load = (is_null($file) ? public_path($_file_name) : storage_path("app/public/$_file_name"));
                            if (file_exists($_file_path_load)) {
                                $_file = Image::make($_file_path_load);
                                $_file_path_file = "{$_file_path}/{$_file_name}";
                                $_file_name_webp = explode('.', $_file_name);
                                $_file_path_webp = "{$_file_path}/{$_file_name_webp[0]}.webp";
                                $_file->save(public_path($_file_path_file), 90);
                                ImageOptimizer::optimize(public_path($_file_path_file));
                                $_file->encode('webp')
                                    ->save(public_path($_file_path_webp));
                            } else {
                                return NULL;
                            }
                        }
                        $_file_path = formalize_path($_file_path_file, $_options['no_last_modify']);
                    } elseif (isset($file->filemime) && in_array($file->filemime, $_images_mimetype_no_generate)) {
                        $_file_path = "/storage/{$file->filename}";
                        $_file_content = Storage::disk('public')->get($file->filename);
                    } elseif (isset($file->filemime) && in_array($file->filemime, $_images_mimetype_svg)) {
                        $_file_path = "/storage/{$file->filename}";
                        $_options['attributes']['uk-svg'] = FALSE;
                    } elseif (is_string($file)) {
                        $_file_content = Storage::disk('base')->get($file);
                    } else {
                        $_file_path = formalize_path('no-image.png', $_options['no_last_modify']);
                    }
                }
            } else {
                $_file_path = formalize_path("{$_file_path}/{$_file_name}", $_options['no_last_modify']);
            }
            if ($_options['only_way']) {
                return $_file_path;
            } elseif ($_file_content) {
                return $_file_content;
            } else {
                if(isset($_options['attributes']['title'])){
                    $_options['attributes']['title'] = str_replace([
                        "'",
                        '"'
                    ], '', $_options['attributes']['title']);
                }
                if(isset($_options['attributes']['alt'])){
                    $_options['attributes']['alt'] = str_replace([
                        "'",
                        '"'
                    ], '', $_options['attributes']['alt']);
                }
                $_attributes = render_attributes($_options['attributes']);
                $_file_path_webp = str_replace(['.jpeg', '.jpg', '.png', '.gif', '.JPEG', '.JPG', '.GIF', '.PNG'], '.webp', $_file_path);
                $_output = '<picture>';
                $_output .= '<source type="image/webp" srcset="' . $_file_path_webp .'" uk-img>';
                $_output .= '<source type="' . ($_no_image ? 'image/jpeg' : ($file->filemime ?? NULL)) . '" srcset="' . $_file_path .'" uk-img>';
				$_output .= '<img src="' . $_file_path . '" ' . $_attributes . ' uk-img>';
                $_output .= '</picture>';
                return $_output;
            }
        }
    }

    if (!function_exists('preview_file_render')) {
        function preview_file_render($file, $options)
        {
            $_default = [
                'field' => NULL,
                'view'  => FALSE
            ];
            $_options = array_merge_recursive_distinct($_default, $options);
            $_images_mimeType = [
                'image/jpeg',
                'image/png',
                'image/gif',
            ];
            $_template = in_array($file->filemime, $_images_mimeType) ? 'image_preview' : 'file_preview';

            return view("backend.partials.{$_template}", compact('file', '_options'))
                ->render();
        }
    }

if (!function_exists('content_render')) {
    function content_render($model, $object = 'body')
    {
        $_content = NULL;
        if (is_object($model)) {
            $_content = $model->{$object};
            preg_match_all('|@short\((.*?)\)|xs', $_content, $_shorts);
            $_variables = NULL;
            if (count($_shorts[0]) && isset($_shorts[1]) && $_shorts[1]) {
                $_models_config = config('os_shortcut');
                foreach ($_shorts[0] as $_index_short => $_data_short) {
                    $_values = explode(';', $_shorts[1][$_index_short]);
                    $_variable = [
                        'code'    => $_data_short,
                        'replace' => NULL
                    ];
                    foreach ($_values as &$item) $item = trim($item);
                    if (isset($_values[0]) && $_values[0]) {
                        if (isset($_models_config[$_values[0]])) {
                            $_entity_data = $_models_config[$_values[0]];
                            $_entity_id = $_values[1] ?? $model->id;
                            $_entity_options = $_values[2] ?? [];
                            if ($_entity_options) {
                                $_entity_options = explode(',', $_entity_options);
                                $__entity_options = [];
                                if (is_array($_entity_options) && $_entity_options) {
                                    foreach ($_entity_options as &$_option) $_option = explode(':', $_option);
                                    foreach ($_entity_options as $_option) {
                                        $__entity_options[$_option[0]] = $_option[1] ?? TRUE;
                                    }
                                }
                                $_entity_options = $__entity_options;
                            }
                            $_entity = new $_entity_data['model'];
                            if (method_exists($_entity, 'getShortcut')) {
                                if($_entity_data['multiple']){
                                    $_entity = new $_entity_data['model'];
                                    $_entity_options['items'] = $_entity_data['model']::whereIn($_entity_data['primary'], explode(',', $_entity_id))
                                        ->active()
                                        ->remember(REMEMBER_LIFETIME)
                                        ->orderBy('sort')
                                        ->get();
                                }else{
                                    $_entity = $_entity_data['model']::where($_entity_data['primary'], $_entity_id)
                                        ->active()
                                        ->remember(REMEMBER_LIFETIME)
                                        ->first();
                                }
                                $_entity_options['type'] = $_values[0];
                                if ($_entity) $_variable['replace'] = $_entity->getShortcut($_entity_options);
                            }
                        }
                        //                            switch($_value[0]) {
                        //                                case 'medias':
                        //                                    $_files = $model->_medias();
                        //                                    $_variable['replace'] = $model->_short_code($_files, 'medias');
                        //                                    break;
                        //                                case 'files':
                        //                                    $_files = $model->_medias('files');
                        //                                    $_variable['replace'] = $model->_short_code($_files, 'files');
                        //                                    break;
                        //                            }
                    }
                    $_variables[] = $_variable;
                }
                foreach ($_variables as $_replace_code) {
                    $_content = str_replace($_replace_code['code'], $_replace_code['replace'], $_content);
                }
            }
        }

        return $_content ? replace_spaces($_content) : NULL;
    }
}

//    if (!function_exists('content_render')) {
//        function content_render($model, $object = 'body')
//        {
//            $_content = NULL;
//            if (is_object($model)) {
//                $_content = $model->{$object};
//                preg_match_all('|@short\((.*?)\)|xs', $_content, $_shorts);
//                $_variables = NULL;
//                if (count($_shorts[0]) && isset($_shorts[1]) && $_shorts[1]) {
//                    $_models_config = config('os_shortcut');
//                    foreach ($_shorts[0] as $_index_short => $_data_short) {
//                        $_values = explode(';', $_shorts[1][$_index_short]);
//                        $_variable = [
//                            'code'    => $_data_short,
//                            'replace' => NULL
//                        ];
//                        foreach ($_values as &$item) $item = trim($item);
//                        if (isset($_values[0]) && $_values[0]) {
//                            if (isset($_models_config[$_values[0]])) {
//                                $_entity_data = $_models_config[$_values[0]];
//                                $_entity_id = $_values[1] ?? $model->id;
//                                $_entity_options = $_values[2] ?? [];
//                                if ($_entity_options) {
//                                    $_entity_options = explode(',', $_entity_options);
//                                    $__entity_options = [];
//                                    if (is_array($_entity_options) && $_entity_options) {
//                                        foreach ($_entity_options as &$_option) $_option = explode(':', $_option);
//                                        foreach ($_entity_options as $_option) {
//                                            $__entity_options[$_option[0]] = $_option[1] ?? TRUE;
//                                        }
//                                    }
//                                    $_entity_options = $__entity_options;
//                                }
//                                $_entity = new $_entity_data['model'];
//                                if (method_exists($_entity, 'getShortcut')) {
//                                    $_entity = $_entity_data['model']::where($_entity_data['primary'], $_entity_id)
//                                        ->active()
//                                        ->remember(REMEMBER_LIFETIME)
//                                        ->first();
//                                    $_entity_options['type'] = $_values[0];
//                                    if ($_entity) $_variable['replace'] = $_entity->getShortcut($_entity_options);
//                                }
//                            }
//                            //                            switch($_value[0]) {
//                            //                                case 'medias':
//                            //                                    $_files = $model->_medias();
//                            //                                    $_variable['replace'] = $model->_short_code($_files, 'medias');
//                            //                                    break;
//                            //                                case 'files':
//                            //                                    $_files = $model->_medias('files');
//                            //                                    $_variable['replace'] = $model->_short_code($_files, 'files');
//                            //                                    break;
//                            //                            }
//                        }
//                        $_variables[] = $_variable;
//                    }
//                    foreach ($_variables as $_replace_code) {
//                        $_content = str_replace($_replace_code['code'], $_replace_code['replace'], $_content);
//                    }
//                }
//            }
//
//            return $_content ? replace_spaces($_content) : NULL;
//        }
//    }

    if (!function_exists('teaser_render')) {
        function teaser_render($entity, $count_word = 130)
        {
            $_response = NULL;
            if (is_object($entity)) {
                $_object = $entity->body;
                if ($entity->hasAttribute('teaser') && $entity->teaser) $_object = $entity->teaser;
                $_response = Arr::get(preg_split('/<div style=\"page-break-after\:always\">(.*?)<\/div>/s', $_object), 0);
                if ($_response) {
                    $_response = truncate_string($_response, [
                        'count_word' => $count_word
                    ]);
                }
                $_response = str_replace("\r\n", NULL, nl2br($_response));
                $_response = preg_replace("/(<br>|<\/br>|<br \/>){2,}/s", '<br>', nl2br($_response));
            }

            return $_response;
        }
    }

    if (!function_exists('breadcrumb_render')) {
        function breadcrumb_render($options = [])
        {
            $_wrap = wrap()->get();
            if (!isset($_wrap['locale'])) $_wrap['locale'] = DEFAULT_LOCALE;
            $_options = array_merge([
                'entity' => NULL,
                'parent' => NULL,
            ], $options);
            $_breadcrumb = collect([]);
            if ($_options['entity']) {
                $_position = 2;
                $_entity_class_basename = strtolower(class_basename($_options['entity']));
                switch ($_entity_class_basename) {
                    case 'tag':
                        $_title = $_options['entity']->breadcrumb_title ? : $_options['entity']->title;
                        $_breadcrumb->push([
                            'name'     => $_title . ($_wrap['seo']['page_number_suffix'] ?? NULL),
                            'url'      => $_options['entity']->generate_url,
                            'position' => $_position
                        ]);
                        break;
                    case 'brand':
                        $_title = $_options['entity']->breadcrumb_title ? : $_options['entity']->title;
                        $_breadcrumb->push([
                            'name'     => $_title . ($_wrap['seo']['page_number_suffix'] ?? NULL),
                            'url'      => $_options['entity']->generate_url,
                            'position' => $_position
                        ]);
                        break;
                    case 'category':
                        if ($_categories_level = $_options['entity']->getBreadcrumb()) {
                            foreach ($_categories_level as $_categories) {
                                $_level = [];
                                if (count($_categories) > 1) {
                                    foreach ($_categories as $_category) {
                                        $_level[] = [
                                            'name' => $_category->breadcrumb_title ? : $_category->title,
                                            'url'  => $_category->generate_url,
                                        ];
                                    }
                                    $_breadcrumb->push([
                                        'name'     => $_level[0]['name'],
                                        'url'      => $_level[0]['url'],
                                        'items'    => $_level,
                                        'position' => $_position
                                    ]);
                                } else {
                                    $_first = array_shift($_categories);
                                    $_breadcrumb->push([
                                        'name'     => $_first->breadcrumb_title ? : $_first->title,
                                        'url'      => $_first->generate_url,
                                        'position' => $_position
                                    ]);
                                }
                                $_position++;
                            }
                        }
                        if ($_options['entity']->filterPage && $_options['entity']->originalData['title']) {
                            $_title = $_options['entity']->originalData['breadcrumb_title'] ? : $_options['entity']->originalData['title'];
                            $_breadcrumb->push([
                                'name'     => $_title,
                                'url'      => $_options['entity']->generate_url,
                                'position' => $_position
                            ]);
                            $_position++;
                        }
                        $_title = $_options['entity']->breadcrumb_title ? : $_options['entity']->title;
                        $_breadcrumb->push([
                            'name'     => $_title . ($_wrap['seo']['page_number_suffix'] ?? NULL),
                            'url'      => $_options['entity']->generate_url,
                            'position' => $_position
                        ]);

                        break;
                    case 'product':
                        $_title = $_options['entity']->breadcrumb_title ? : $_options['entity']->title;
                        $_categories_level = $_options['entity']->getBreadcrumb();
                        if ($_categories_level) {
                            foreach ($_categories_level as $_categories) {
                                foreach ($_categories as $_category) {
                                    $_breadcrumb->push([
                                        'name'     => $_category->breadcrumb_title ? : $_category->title,
                                        'url'      => $_category->generate_url,
                                        'items'    => NULL,
                                        'position' => $_position
                                    ]);
                                    $_position++;
                                }
                            }
                        }
                        $_breadcrumb->push([
                            'name'     => $_title . ($_wrap['seo']['page_number_suffix'] ?? NULL),
                            'url'      => $_options['entity']->generate_url,
                            'position' => $_position
                        ]);
                        break;
                    default:
                        if ($_entity_class_basename == 'page' && $_options['entity']->type == 'front') break;


                        if ($_options['parent']) {
                            $_title = $_options['parent']->breadcrumb_title ? : $_options['parent']->title;
                            $_breadcrumb->push([
                                'name'     => $_title,
                                'url'      => $_options['parent']->generate_url,
                                'position' => $_position
                            ]);
                        }
                        $_position++;
                        $_title = $_options['entity']->breadcrumb_title ? : $_options['entity']->title;
                        $_breadcrumb->push([
                            'name'     => $_title . ($_wrap['seo']['_page_number_suffix'] ?? NULL),
                            'url'      => $_options['entity']->generate_url,
                            'position' => $_position
                        ]);
                        break;

//                        if ($_options['parent']) {
//                            foreach ($_options['parent'] as $_parent) {
//                                $_title = $_parent->breadcrumb_title ? : $_parent->title;
//                                $_breadcrumb->push([
//                                    'name'     => $_title,
//                                    'url'      => $_parent->generate_url,
//                                    'position' => $_position
//                                ]);
//                            }
//
//                        }else {
//                        $_position++;
//                        $_title = $_options['entity']->breadcrumb_title ? $_options['entity']->breadcrumb_title : $_options['entity']->title;
//                        $_breadcrumb->push([
//                            'name'     => $_title . ($_wrap['seo']['page_number_suffix'] ?? NULL),
//                            'url'      => $_options['entity']->url ?? $_options['entity']->generate_url,
//                            'position' => $_position
//                        ]);
//                        }

//                        break;
                }
            }
            if ($_breadcrumb->isNotEmpty()) {
                $_breadcrumb->prepend([
                    'name'     => trans('pages.titles.home'),
                    'url'      => _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')),
                    'position' => 1
                ]);
            } else {
                $_breadcrumb = NULL;
            }

            return $_breadcrumb;
        }
    }

    if (!function_exists('field_render')) {
        function field_render($name, $options = [])
        {
            $_item = new Fields($name, $options);

            return $_item->_render();
        }
    }

    if (!function_exists('page_render')) {
        function page_render($entity, $options = [])
        {
            $_item = NULL;
            $_options = array_merge([
                'view'      => NULL,
                'view_mode' => 'full'
            ], $options);
            if ($entity instanceof Page) {
                $_item = $entity;
            } elseif (is_numeric($entity)) {
                $_item = Page::where('id', $entity)
                    ->active()
                    ->with([
                        '_alias' => function ($q) {
                            $q->remember(REMEMBER_LIFETIME);
                        }
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            } elseif (is_string($entity)) {
                $_item = Page::where('type', $entity)
                    ->active()
                    ->with([
                        '_alias'         => function ($q) {
                            $q->remember(REMEMBER_LIFETIME);
                        },
                        '_display_rules' => function ($q) {
                            $q->remember(REMEMBER_LIFETIME);
                        }
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            }
            if ($_item) return $_item->_render($_options);

            return $_item;
        }
    }

    if (!function_exists('form_generate')) {
        function form_generate($options = [])
        {
            $_form = (object)array_merge([
                'id'                => NULL,
                'action'            => NULL,
                'form_class'        => NULL,
                'button_send_class' => NULL,
                'button_send_title' => trans('frontend.button.send'),
                'fields'            => [],
                'buttons'           => [],
                'title'             => NULL,
                'body'              => NULL,
                'prefix'            => NULL,
                'suffix'            => NULL,
                'modal'             => FALSE,
                'ajax'              => TRUE,
            ], $options);

            return View::make('frontend.default.forms.form_generate', compact('_form'))
                ->render();
        }
    }

    if (!function_exists('block_render')) {
        function block_render($entity, $options = [])
        {
            $_item = NULL;
            try {
                $_options = array_merge([
                    'view'  => NULL,
                    'index' => NULL,
                ], $options);
                if ($entity instanceof Block) {
                    $_item = $entity;
                } elseif (is_numeric($entity)) {
                    $_item = Block::where('id', $entity)
                        ->with([
                            '_display_rules' => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            }
                        ])
                        ->remember(REMEMBER_LIFETIME)
                        ->first();
                }
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access)) return $_item->_render($_options);
                }

                return NULL;
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

    if (!function_exists('banner_render')) {
        function banner_render($entity, $options = [])
        {
            $_item = NULL;
            try {
                $_options = array_merge([
                    'view'  => NULL,
                    'index' => NULL,
                ], $options);
                if ($entity instanceof Banner) {
                    $_item = $entity;
                } elseif (is_numeric($entity)) {
                    $_item = Banner::where('id', $entity)
                        ->with([
                            '_display_rules' => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            }
                        ])
                        ->remember(REMEMBER_LIFETIME)
                        ->first();
                }
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access)) {
                        return $_item->_render($_options);
                    }
                }

                return NULL;
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

    if (!function_exists('form_render')) {
        function form_render($entity, $options = [])
        {
            $_item = NULL;
            try {
                $_options = array_merge([
                    'view'  => NULL,
                    'index' => NULL,
                ], $options);
                if ($entity instanceof Forms) {
                    $_item = $entity;
                } elseif (is_numeric($entity)) {
                    $_item = Forms::where('id', $entity)
                        ->with([
                            '_display_rules' => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            }
                        ])
                        ->remember(REMEMBER_LIFETIME)
                        ->first();
                }
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access)) return $_item->_render($_options);
                }

                return NULL;
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

    if (!function_exists('faq_block_render')) {
        function faq_block_render($options = [])
        {
            $_item = NULL;
            try {
                $_item = NULL;
                $_options = array_merge([
                    'view' => NULL
                ], $options);
                $_entity = new Faq();

                return $_entity->_render_block($_options);
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

    if (!function_exists('menu_render')) {
        function menu_render($entity, $options = [])
        {
            $_item = NULL;
            try {
                $_options = array_merge([
                    'view'  => NULL,
                    'index' => NULL,
                ], $options);
                if ($entity instanceof Menu) {
                    $_item = $entity;
                } elseif (is_numeric($entity)) {
                    $_item = Menu::where('id', $entity)
                        ->with([
                            '_display_rules' => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            }
                        ])
                        ->remember(REMEMBER_LIFETIME)
                        ->first();
                }
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access)) return $_item->_render($_options);
                }

                return NULL;
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

//    if (!function_exists('advantage_block_render')) {
//        function advantage_block_render($entity, $options = [])
//        {
//            $_item = NULL;
//            try {
//                $_options = array_merge([
//                    'view' => NULL
//                ], $options);
//                if ($entity instanceof Advantage) {
//                    $_item = $entity;
//                } elseif (is_numeric($entity)) {
//                    $_item = Advantage::active()
//                        ->with([
//                            '_items'         => function ($q) {
//                                $q->remember(REMEMBER_LIFETIME);
//                            },
//                            '_display_rules' => function ($q) {
//                                $q->remember(REMEMBER_LIFETIME);
//                            }
//                        ])
//                        ->remember(REMEMBER_LIFETIME)
//                        ->firstOrFail();
//                }
//                if ($_item && $_item->view_access) {
//                    if (is_bool($_item->view_access)) return $_item->_render($_options);
//                }
//
//                return NULL;
//            } catch (Exception $exception) {
//                return $_item;
//            }
//        }
//    }

if (!function_exists('advantage_block_render')) {
    function advantage_block_render($entity, $options = [])
    {
        $_item = NULL;
        try {
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            if ($entity instanceof Advantage) {
                $_item = $entity;
            } elseif (is_numeric($entity)) {
                $_item = Advantage::where('id', $entity)
                    ->with([
                        '_display_rules'
                    ])
                    ->remember(15)
                    ->first();
            }
            if ($_item && $_item->view_access) {
                if (is_bool($_item->view_access)) {
                    return $_item->_render($_options);
                } else {
                    $_item->invisible = TRUE;

                    return $_item->_render($_options);
                }
            }

            return NULL;
        } catch (Exception $exception) {
            return $_item;
        }
    }
}

    if (!function_exists('slider_render')) {
        function slider_render($entity, $options = [])
        {
            $_item = NULL;
            try {
                $_options = array_merge([
                    'view'  => NULL,
                    'index' => NULL,
                ], $options);
                if ($entity instanceof Slider) {
                    $_item = $entity;
                } elseif (is_numeric($entity)) {
                    $_item = Slider::where('id', $entity)
                        ->with([
                            '_display_rules' => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            },
                            '_items'         => function ($q) {
                                $q->remember(REMEMBER_LIFETIME);
                            }
                        ])
                        ->remember(REMEMBER_LIFETIME)
                        ->first();
                }
                if ($_item && $_item->view_access) {
                    if (is_bool($_item->view_access)) return $_item->_render($_options);
                }

                return NULL;
            } catch (Exception $exception) {
                return $_item;
            }
        }
    }

    if (!function_exists('menu_item_render')) {
        function menu_item_render($item, $level = 0)
        {
            $_output = NULL;
            $level++;
            $item['item']['attributes']['class'][] = "level-item-{$level}";
            $_output = '<li ' . render_attributes($item['item']['wrapper']) . '>';
            $_output .= $item['item']['prefix'];
            if ($item['item']['active'] || is_null($item['item']['path'])) {
                $_output .= '<span class="uk-navbar-toggle">' . $item['item']['title'] . '</span>';
            } else {
                $_output .= '<a ' . render_attributes($item['item']['attributes']) . '>' . $item['item']['title'] . '</a>';
            }
            if ($item['children']->isNotEmpty()) {
                $_output .= '<ul class="uk-nav-sub">';
                foreach ($item['children'] as $_sub_item) {
                    $_output .= menu_item_render($_sub_item, $level);
                }
                $_output .= '</ul>';
            }
            $_output .= $item['item']['suffix'];
            $_output .= '</li>';

            return $_output;
        }
    }

    if (!function_exists('menu_8_on_page_item_render')) {
        function menu_8_on_page_item_render($item, $level = 0)
        {
            $_output = NULL;
            $level++;
            if ($level == 1) {
                $item['item']['attributes']['class'][] = 'uk-text-bold uk-text-uppercase';
            }
            $item['item']['attributes']['class'][] = "level-item-{$level}";
            $_output = '<li ' . render_attributes($item['item']['wrapper']) . '>';
            $_output .= $item['item']['prefix'];
            if ($item['item']['active'] || is_null($item['item']['path'])) {
                $_output .= '<a ' . render_attributes($item['item']['attributes']) . '>' . $item['item']['title'] . '</a>';
            } else {
                $_output .= '<a ' . render_attributes($item['item']['attributes']) . '>' . $item['item']['title'] . '</a>';
            }
            if ($item['children']->isNotEmpty()) {
                $_output .= '<ul class="uk-nav-sub">';
                foreach ($item['children'] as $_sub_item) {
                    $_output .= menu_8_on_page_item_render($_sub_item, $level);
                }
                $_output .= '</ul>';
            }
            $_output .= $item['item']['suffix'];
            $_output .= '</li>';

            return $_output;
        }
    }

    if (!function_exists('menu_8_on_front_page_item_render')) {
        function menu_8_on_front_page_item_render($item, $level = 0)
        {
            $_output = NULL;
            $level++;
            if ($level == 1) {
                $item['item']['wrapper']['class'][] = 'uk-width-1-5@l uk-width-1-3@s';
                $item['item']['attributes']['class'][] = 'uk-text-uppercase';
            }
            $item['item']['attributes']['class'][] = "level-item-{$level}";
            $_output = '<li ' . render_attributes($item['item']['wrapper']) . '>';
            $_output .= $item['item']['prefix'];
            if ($item['item']['active'] || is_null($item['item']['path'])) {
                $_output .= '<span class="uk-navbar-toggle uk-text-primary">' . $item['item']['title'] . '</span>';
            } else {
                $_output .= '<a ' . render_attributes($item['item']['attributes']) . '>' . $item['item']['title'] . '</a>';
            }
            if ($item['children']->isNotEmpty()) {
                $_output .= '<ul class="sub-nav">';
                foreach ($item['children'] as $_sub_item) {
                    $_output .= menu_8_on_front_page_item_render($_sub_item, $level);
                }
                $_output .= '</ul>';
            }
            $_output .= $item['item']['suffix'];
            $_output .= '</li>';

            return $_output;
        }
    }
