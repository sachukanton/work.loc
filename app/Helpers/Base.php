<?php

use App\Models\Components\Journal;
use App\Models\Components\Variable;
use App\Models\File\File;
use App\Models\Seo\UrlAlias;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Storage;

if (!function_exists('wrap')) {
    function wrap()
    {
        return app('wrap');
    }
}

if (!function_exists('device')) {
    function device($parameters = [])
    {
        return Container::getInstance()->make('device', $parameters);
    }
}

if (!function_exists('array_undot')) {
    function array_undot($dotNotationArray)
    {
        $array = [];
        foreach ($dotNotationArray as $key => $value) {
            array_set($array, $key, $value);
        }

        return $array;
    }
}

if (!function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $_merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($_merged[$key]) && is_array($_merged[$key])) {
                $_merged[$key] = array_merge_recursive_distinct($_merged[$key], $value);
            } else {
                $_merged[$key] = $value;
            }
        }

        return $_merged;
    }
}

if (!function_exists('is_assoc_array')) {
    function is_assoc_array($array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
        //            return array_keys($array) !== range(0, count($array)-1);
    }
}

if (!function_exists('is_whole_int')) {
    function is_whole_int($value)
    {
        return is_numeric($value) && floor($value) == $value && $value > 0;
    }
}

if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);

        return (json_last_error() == 0);
    }
}

if (!function_exists('number_format_short')) {
    function number_format_short($number, $precision = 1)
    {
        if ($number > 0 && $number < 1000000) {
            // 1k-999k
            $number = (float)($number / 1000);
            $_format = is_whole_int($number) ? $number : number_format($number, $precision);
            $_suffix = 'K';
        } else {
            if ($number >= 1000000 && $number < 1000000000) {
                // 1m-999m
                $_format = number_format($number / 1000000, $precision);
                $_suffix = 'M';
            } else {
                if ($number >= 1000000000 && $number < 1000000000000) {
                    // 1b-999b
                    $_format = number_format($number / 1000000000, $precision);
                    $_suffix = 'B';
                } else {
                    if ($number >= 1000000000000) {
                        // 1t+
                        $_format = number_format($number / 1000000000000, $precision);
                        $_suffix = 'T';
                    }
                }
            }
        }

        return !empty($_format . $_suffix) ? $_format . $_suffix : 0;
    }
}

if (!function_exists('clear_html')) {
    function clear_html($html)
    {
        preg_match_all('!(<(?:code|pre|script).*>[^<]+</(?:code|pre|script)>)!', $html, $pre);
        $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html);
        $html = preg_replace('#<!–[^\[].+–>#', '', $html);
        $html = preg_replace('/[\r\n\t]+/', ' ', $html);
        $html = preg_replace('/>[\s]+</', '> <', $html);
        $html = preg_replace('/>[\s]+/', '> ', $html);
        $html = preg_replace('/[\s]+</', ' <', $html);
        $html = preg_replace('/[\s]+/', ' ', $html);
        if (!empty($pre[0])) {
            foreach ($pre[0] as $tag) {
                $html = preg_replace('!#pre#!', $tag, $html, 1);
            }
        }

        return $html;
    }
}

if (!function_exists('clear_html_attribute')) {
    function clear_html_attribute($html, $attribute = 'style')
    {
        return preg_replace('/' . $attribute . '=\\"[^\\"]*\\"/', '', $html);
    }
}

if (!function_exists('clear_duplicate_spaces')) {
    function clear_duplicate_spaces($html)
    {
        $html = htmlspecialchars(clear_html_attribute($html));

        return str_replace('&nbsp;', ' ', html_entity_decode($html));
    }
}

if (!function_exists('transcription_string')) {
    function transcription_string($string)
    {
        $string = trim(strip_tags($string));
        $_transcription = [
            'й' => 'q',
            'ц' => 'w',
            'у' => 'e',
            'к' => 'r',
            'е' => 't',
            'н' => 'y',
            'г' => 'u',
            'ш' => 'i',
            'щ' => 'o',
            'з' => 'p',
            'х' => '[',
            'ъ' => ']',
            'ф' => 'a',
            'ы' => 's',
            'в' => 'd',
            'а' => 'f',
            'п' => 'g',
            'р' => 'h',
            'о' => 'j',
            'л' => 'k',
            'д' => 'l',
            'ж' => ';',
            'э' => '\'',
            'я' => 'z',
            'ч' => 'x',
            'с' => 'c',
            'м' => 'v',
            'и' => 'b',
            'т' => 'n',
            'ь' => 'm',
            'б' => ',',
            'ю' => '.',
            '.' => '/',
            'Й' => 'Q',
            'Ц' => 'W',
            'У' => 'E',
            'К' => 'R',
            'Е' => 'T',
            'Н' => 'Y',
            'Г' => 'U',
            'Ш' => 'I',
            'Щ' => 'O',
            'З' => 'P',
            'Х' => '{',
            'Ъ' => '}',
            'Ф' => 'A',
            'Ы' => 'S',
            'В' => 'D',
            'А' => 'F',
            'П' => 'G',
            'Р' => 'H',
            'О' => 'J',
            'Л' => 'K',
            'Д' => 'L',
            'Ж' => ':',
            'Э' => '"',
            'Я' => 'Z',
            'Ч' => 'X',
            'С' => 'C',
            'М' => 'V',
            'И' => 'B',
            'Т' => 'N',
            'Ь' => 'M',
            'Б' => '<',
            'Ю' => '>',
            ',' => '?'
        ];
        if (preg_match('/[A-z]+/i', $string)) $_transcription = array_flip($_transcription);

        return strtr($string, $_transcription);
    }
}

if (!function_exists('similar_split_letters')) {
    function similar_split_letters($string)
    {
        $_letters = [
            ['е' => 'и'],
            ['о' => 'а'],
            ['и' => 'а'],
            ['в' => 'ф'],
            ['м' => 'л'],
            ['н' => 'л'],
            ['п' => 'н'],
            ['б' => 'п'],
            ['к' => 'п'],
            ['б' => 'в'],
            ['д' => 'т'],
            ['п' => 'л'],
            ['х' => 'к'],
            ['н' => 'м'],
            ['и' => 'е'],
            ['а' => 'о'],
            ['а' => 'и'],
            ['ф' => 'в'],
            ['л' => 'м'],
            ['л' => 'н'],
            ['н' => 'п'],
            ['п' => 'б'],
            ['п' => 'к'],
            ['в' => 'б'],
            ['т' => 'д'],
            ['л' => 'п'],
            ['к' => 'х'],
            ['м' => 'н']
        ];
        $_response = [];
        foreach ($_letters as $_letter) {
            $_replaced = str_replace(array_keys($_letter), array_values($_letter), $string);
            if ($string != $_replaced) $_response[] = $_replaced;
        }

        return $_response;
    }
}

if (!function_exists('plural_string')) {
    function plural_string($n, $string, $tag = NULL)
    {
        $_plural = explode('|', $string);
        foreach ($_plural as &$_p) if ($_p) $_p = trans($_p);
        if (!$n) return $_plural[0];
        if ($n % 10 == 1 && $n % 100 != 11) {
            return $tag ? "{$n}&nbsp;<{$tag}>{$_plural[1]}</{$tag}>" : "{$n}&nbsp;{$_plural[1]}";
        } elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20)) {
            return $tag ? "{$n}&nbsp;<{$tag}>{$_plural[2]}</{$tag}>" : "{$n}&nbsp;{$_plural[2]}";
        } else {
            return $tag ? "{$n}&nbsp;<{$tag}>{$_plural[3]}</{$tag}>" : "{$n}&nbsp;{$_plural[3]}";
        }
    }
}

if (!function_exists('robots')) {
    function robots($save = FALSE)
    {
        if ($save) {
            $robots = request()->input('robots_txt');
            if ($robots) Storage::disk('base')->put('robots.txt', $robots);

            return TRUE;
        }
        $robots = NULL;
        if (Storage::disk('base')->exists('robots.txt')) $robots = Storage::disk('base')->get('robots.txt');

        return $robots;
    }
}

if (!function_exists('f_get')) {
    function f_get($fid = NULL)
    {
        $_response = NULL;
        $_disk = Storage::disk('public');
        if (is_numeric($fid)) {
            $_file = File::where('id', $fid)
                ->first();
            if ($_file && $_disk->exists($_file->filename)) $_response = $_file;
        } elseif (is_array($fid)) {
            foreach ($fid as $_file_fid) {
                $_file = File::where('id', $_file_fid)
                    ->first();
                if ($_file && $_disk->exists($_file->filename)) $_response[] = $_file;
            }
            if ($_response) $_response = collect($_response);
        }

        return $_response;
    }
}

if (!function_exists('f_save')) {
    function f_save($file, $request = [])
    {
        if (count($request)) {
            $_save = FALSE;
            if (isset($request['title']) && $request['title'] != $file->title) {
                $_save = TRUE;
                $file->title = $request['title'];
            }
            if (isset($request['alt']) && $request['alt'] != $file->alt) {
                $_save = TRUE;
                $file->alt = $request['alt'];
            }
            if (isset($request['description']) && $request['description'] != $file->description) {
                $_save = TRUE;
                $file->description = $request['description'];
            }
            if ($_save) $file->save();
        }

        return $file;
    }
}

if (!function_exists('update_last_modified_timestamp')) {
    function update_last_modified_timestamp()
    {
        \ConfigWriter::write('os_seo', ['last_modified_timestamp' => time()]);
    }
}

if (!function_exists('transform_price')) {
    function transform_price($price, $currency = NULL, $default_currency = FALSE)
    {
        $_currencies = config('os_currencies');
        if ($default_currency) {
            if (is_string($default_currency)) {
                $_current_currency = wrap()->get("loads.currency.all.{$default_currency}");
            } elseif (is_array($default_currency)) {
                $_current_currency = $default_currency;
            }
        } else {
            $_current_currency = wrap()->get('loads.currency.current');
        }
        $_decimals = (int)$_current_currency['precision'];
        if (is_array($price) && isset($price['currency'])) {
            $_entity_currency = $_currencies['currencies'][$price['currency']]['use'] ? $_currencies['currencies'][$price['currency']] : $_currencies['currencies'][DEFAULT_CURRENCY];;
            $_price = $price['price'];
        } elseif ($currency) {
            $_entity_currency = $_currencies['currencies'][$currency]['use'] ? $_currencies['currencies'][$currency] : $_currencies['currencies'][DEFAULT_CURRENCY];
            $_price = $price;
        } else {
            $_entity_currency = $_currencies['currencies'][DEFAULT_CURRENCY];
            $_price = $price;
        }
        if (($_current_currency['key'] == DEFAULT_CURRENCY) && (USE_MULTI_CURRENCIES && $_price && ($_entity_currency['iso_code'] != $_current_currency['iso_code']) && $_entity_currency['ratio'])) {
            $_price = $_price * (float)$_entity_currency['ratio'];
        } elseif (USE_MULTI_CURRENCIES && $_price && ($_entity_currency['iso_code'] != $_current_currency['iso_code']) && $_entity_currency['ratio']) {
            $_price = $_price * (float)$_entity_currency['ratio'] / (float)$_current_currency['ratio'];
        }
        if ($_current_currency['precision_mode'] == 1 && $_price) {
            if ($_decimals) {
                $_decimals_pow = pow(10, $_decimals);
                $_price = floor((float)$_price * $_decimals_pow) / $_decimals_pow;
            } else {
                $_price = round((float)$_price, 0, PHP_ROUND_HALF_DOWN);
            }
        } elseif ($_current_currency['precision_mode'] == 2 && $_price) {
            $_decimals_pow = pow(10, $_decimals);
            $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
        } elseif ($_current_currency['precision_mode'] == 3 && $_price) {
            $_price = ceil((float)$_price / 10) * 10;
        } elseif ($_current_currency['precision_mode'] == 4 && $_price) {
            $_price = ceil((float)$_price / 100) * 100;
        }
        if (!$_decimals) $_price = (int)$_price;

        return view_price($_price, $price, $currency, $_current_currency);
    }
}

if (!function_exists('view_price')) {
    function view_price($price, $price_original, $currency = NULL, $default_currency = FALSE)
    {
        if ($default_currency) {
            if (is_string($default_currency)) {
                $_current_currency = wrap()->get("loads.currency.all.{$default_currency}");
            } elseif (is_array($default_currency)) {
                $_current_currency = $default_currency;
            }
        } else {
            $_current_currency = wrap()->get('loads.currency.current');
        }
        $_decimals = (int)$_current_currency['precision'];
        if ($_current_currency['precision_mode'] == 3 || $_current_currency['precision_mode'] == 4) $_decimals = 0;
        $_prefix = $_current_currency['prefix'] ? '<span class="currency-prefix">' . trim($_current_currency['prefix']) . '</span> ' : NULL;
        $_suffix = $_current_currency['suffix'] ? ' <span class="currency-suffix">' . trim($_current_currency['suffix']) . '</span>' : NULL;
        $_response = [
            'original' => [
                'price'    => is_array($price_original) && isset($price_original['price']) ? $price_original['price'] : ($price_original ? $price_original : NULL),
                'currency' => is_array($price_original) && isset($price_original['currency']) ? $price_original['currency'] : ($currency ? $currency : NULL)
            ],
            'format'   => [
                'price'        => $price,
                'view_price'   => $price ? number_format($price, $_decimals, ',', ' ') : number_format(0, $_decimals, ',', ''),
                'view_price_2' => $price ? $_prefix . number_format($price, $_decimals, ',', ' ') . $_suffix : $_prefix . number_format(0, $_decimals, ',', ' ') . $_suffix,
            ],
            'currency' => $_current_currency
        ];

        return $_response;
    }
}

if (!function_exists('generate_field_id')) {
    function generate_field_id($name, $form_id = NULL)
    {
        $_prefix = $form_id ? "{$form_id}-" : 'form-field-';

        return str_slug($_prefix . str_replace([
                '.',
                '_'
            ], '_', $name), '-');
    }
}

if (!function_exists('format_phone_number')) {
    function format_phone_number($phone)
    {
        $_codes = [
            '67' => 'k-star',
            '68' => 'k-star',
            '91' => 'k-star',
            '96' => 'k-star',
            '97' => 'k-star',
            '98' => 'k-star',
            '63' => 'life',
            '93' => 'life',
            '73' => 'life',
            '50' => 'mts',
            '66' => 'mts',
            '95' => 'mts',
            '99' => 'mts',
            '61' => 'city',
        ];
        $_phone = htmlspecialchars(clear_phone_number($phone));

        $_phone = str_replace('&nbsp;', '', html_entity_decode($_phone));

        $_phone = str_replace(' ', '', $_phone);


        preg_match('/\((.*?)\)/', $phone, $_phone_number_code_matches);

        $_code = $_phone_number_code_matches[1] ?? NULL;

        $_code_class = $_codes[$_code] ?? NULL;
        if (is_null($_code)) {

            foreach (array_keys($_codes) as $_code_phone) {

                if (str_is("{$_code_phone}*", $_phone)) {
                    $_code = $_code_phone;
                    $_code_class = $_codes[$_code] ?? NULL;
                    break;
                }
            }
        }

        //        $_phone = str_replace($_code, '', $_phone);
        $_phone = mb_eregi_replace("^.{2}(.*)$", '\\1', $_phone);

        preg_match('/^(\d{3})(\d{2})(\d{2})$/', $_phone, $_phone_number_matches);
        if (!count($_phone_number_matches)) preg_match('/^(\d{3})(\d{2})(\d{2})$/', $_phone, $_phone_number_matches);
        if (!count($_phone_number_matches)) preg_match('/^(\d{3})(\d{2})(\d{2})$/', $_phone, $_phone_number_matches);
        unset($_phone_number_matches[0]);
        $_phone_number = implode('&nbsp;', $_phone_number_matches);

        return [
            'original'         => $phone,
            'code'             => $_code,
            'class'            => $_code_class,
            'format_lite'      => "({$_code})&nbsp;{$_phone_number}",
            'format_full'      => "+38&nbsp;({$_code})&nbsp;{$_phone_number}",
            'format_full_2'    => "+38&nbsp;<span class=\"{$_code_class}\">{$_code}</span>&nbsp;{$_phone_number}",
            'format_link_href' => 'tel:+380' . clear_phone_number($phone),
            'format_html'      => "+38&nbsp;<span class=\"{$_code_class}\">{$_code}</span>&nbsp;{$_phone_number}",
            'format_render'    => '<a href="tel:+38' . clear_phone_number($phone) . '">(' . $_code . ')&nbsp;' . $_phone_number . '</a>',
            'format_render_2'  => '+380&nbsp;<span class="' . $_code_class . '">' . $_code . '</span>&nbsp;' . $_phone_number,
            'format_render_3'  => '<a href="tel:' . clear_phone_number($phone) . '">' . $phone . '</a>'
        ];
    }
}

if (!function_exists('clear_phone_number')) {
    function clear_phone_number($phone)
    {
        return preg_replace('/^\+380|^380|\D/m', '', $phone);
    }
}

if (!function_exists('truncate_string')) {
    function truncate_string($string, $options = [])
    {
        $_response = strip_tags(clear_html($string));
        $_options = array_merge([
            'count_word' => 300,
            'bound_word' => TRUE,
            'dotted'     => TRUE,
        ], $options);
        if (iconv_strlen($_response, 'UTF-8') > $_options['count_word']) {
            $_response = mb_substr($_response, 0, $_options['count_word']);
            $_response = rtrim($_response, '!,.-');
            if ($_options['bound_word']) $_response = mb_substr($_response, 0, mb_strrpos($_response, ' '));
            if ($_options['dotted']) $_response .= '...';
        }

        return $_response;
    }
}

if (!function_exists('replace_spaces')) {
    function replace_spaces($string_html)
    {
        $string_html = preg_replace('/>[\s]+</', '><', trim(trim($string_html), '&nbsp;'));
        for ($i = 0, $_tag_open = FALSE, $_tag_close = FALSE; $i < strlen($string_html); $i++) {
            if (($string_html[$i] == ' ') && $_tag_close) {
                $string_html = substr_replace($string_html, '&nbsp;', $i, 1);
            } elseif ($i > 0 && ($string_html[$i - 2] == ' ') && $_tag_open) {
                $string_html = substr_replace($string_html, '&nbsp;', $i - 2, 1);
            } else {
                if ($string_html[$i] == '<') {
                    $_tag_open = TRUE;
                    $_tag_close = FALSE;
                } elseif ($string_html[$i] == '>') {
                    $_tag_close = TRUE;
                    $_tag_open = FALSE;
                } else {
                    $_tag_close = FALSE;
                    $_tag_open = FALSE;
                }
            }
        }

        return $string_html;
    }
}

if (!function_exists('replace_unicode_to_win')) {
    function replace_unicode_to_win($string)
    {
        $_transcription = [
            'А' => '&#1040;',
            'Б' => '&#1041;',
            'В' => '&#1042;',
            'Г' => '&#1043;',
            'Д' => '&#1044;',
            'Е' => '&#1045;',
            'Ж' => '&#1046;',
            'З' => '&#1047;',
            'И' => '&#1048;',
            'Й' => '&#1049;',
            'К' => '&#1050;',
            'Л' => '&#1051;',
            'М' => '&#1052;',
            'Н' => '&#1053;',
            'О' => '&#1054;',
            'П' => '&#1055;',
            'Р' => '&#1056;',
            'С' => '&#1057;',
            'Т' => '&#1058;',
            'У' => '&#1059;',
            'Ф' => '&#1060;',
            'Х' => '&#1061;',
            'Ц' => '&#1062;',
            'Ч' => '&#1063;',
            'Ш' => '&#1064;',
            'Щ' => '&#1065;',
            'Ъ' => '&#1066;',
            'Ы' => '&#1067;',
            'Ь' => '&#1068;',
            'Э' => '&#1069;',
            'Ю' => '&#1070;',
            'Я' => '&#1071;',
            'а' => '&#1072;',
            'б' => '&#1073;',
            'в' => '&#1074;',
            'г' => '&#1075;',
            'д' => '&#1076;',
            'е' => '&#1077;',
            'ж' => '&#1078;',
            'з' => '&#1079;',
            'и' => '&#1080;',
            'й' => '&#1081;',
            'к' => '&#1082;',
            'л' => '&#1083;',
            'м' => '&#1084;',
            'н' => '&#1085;',
            'о' => '&#1086;',
            'п' => '&#1087;',
            'р' => '&#1088;',
            'с' => '&#1089;',
            'т' => '&#1090;',
            'у' => '&#1091;',
            'ф' => '&#1092;',
            'х' => '&#1093;',
            'ц' => '&#1094;',
            'ч' => '&#1095;',
            'ш' => '&#1096;',
            'щ' => '&#1097;',
            'ъ' => '&#1098;',
            'ы' => '&#1099;',
            'ь' => '&#1100;',
            'э' => '&#1101;',
            'ю' => '&#1102;',
            'я' => '&#1103;',
            'Ё' => '&#1025;',
            'ё' => '&#1025;',
        ];

        return strtr($string, array_flip($_transcription));
    }
}

if (!function_exists('spy')) {
    function spy($message, $type = 'info', $alert = FALSE, $error = NULL)
    {
        Journal::create([
            'type'    => $type,
            'message' => $message,
            'error'   => $error,
        ]);
        // todo: в дальнейшем дописать функцию оповещения о конкретных событиях (например ошибках)
    }
}

if (!function_exists('data_encrypt')) {
    function data_encrypt($data)
    {
        $_key = '6LcJ4M8UAAAAAKyDU_EA-M7eh3AXNdwm';
        $_encrypt = serialize($data);
        $_ivLen = openssl_cipher_iv_length($_cipher = "AES-128-CBC");
        $_iv = openssl_random_pseudo_bytes($_ivLen);
        $_cipherTextRaw = openssl_encrypt($_encrypt, $_cipher, $_key, $_options = OPENSSL_RAW_DATA, $_iv);
        $_mac = hash_hmac('sha256', $_cipherTextRaw, $_key, $_as_binary = TRUE);

        return base64_encode($_iv . $_mac . $_cipherTextRaw);
    }
}

if (!function_exists('data_decrypt')) {
    function data_decrypt($string)
    {
        $_key = '6LcJ4M8UAAAAAKyDU_EA-M7eh3AXNdwm';
        $_cipherText = base64_decode($string);
        $_ivLen = openssl_cipher_iv_length($_cipher = "AES-128-CBC");
        $_iv = substr($_cipherText, 0, $_ivLen);
        $_mac = substr($_cipherText, $_ivLen, $_sha2Len = 32);
        $_cipherTextRaw = substr($_cipherText, $_ivLen + $_sha2Len);
        $_plainText = openssl_decrypt($_cipherTextRaw, $_cipher, $_key, $_options = OPENSSL_RAW_DATA, $_iv);
        $_calcMac = hash_hmac('sha256', $_cipherTextRaw, $_key, $as_binary = TRUE);
        if (hash_equals($_mac, $_calcMac)) return unserialize($_plainText);

        return FALSE;
    }
}

if (!function_exists('choice_template')) {
    function choice_template($templates = [])
    {
        $_template = NULL;
        if ($templates) {
            foreach ($templates as $template) if (view()->exists($template) && is_null($_template)) $_template = $template;
        }

        return $_template;
    }
}

if (!function_exists('redirect_paths')) {
    function redirect_paths($paths = [])
    {
        $_response = NULL;
        if ($paths) {
            $_paths_finds = [
                'mask'   => collect([]),
                'normal' => collect([])
            ];
            foreach ($paths as $_path) {
                $_paths_finds[(ends_with($_path, '*') ? 'mask' : 'normal')]->push($_path);
            }
            if ($_paths_finds['mask']) {
                $_url_exists = UrlAlias::when($_paths_finds, function ($_query) use ($_paths_finds) {
                    $_i = 0;
                    foreach ($_paths_finds['mask'] as $_mask) {
                        $_query_mask = trim($_mask, '*');
                        if ($_i == 0) {
                            $_query->where('alias', 'like', "{$_query_mask}%");
                        } else {
                            $_query->orWhere('alias', 'like', "{$_query_mask}%");
                        }
                        $_i++;
                    }
                })
                    ->pluck('alias');
                if ($_url_exists->isNotEmpty()) {
                    foreach ($_paths_finds['mask'] as $_mask) {
                        $_exist = $_url_exists->filter(function ($_i) use ($_mask) {
                            return str_is($_mask, $_i);
                        })->count();
                        if (!$_exist) {
                            $_response['add'][] = $_mask;
                        } else {
                            $_response['excluded'][] = $_mask;
                        }
                    }
                } else {
                    foreach ($_paths_finds['mask'] as $_mask) {
                        $_response['add'][] = $_mask;
                    }
                }
            }
            if ($_paths_finds['normal']) {
                $_url_exists = UrlAlias::whereIn('alias', $_paths_finds['normal'])
                    ->pluck('alias', 'alias');
                $_paths_finds['normal']->each(function ($_i) use (&$_response, $_url_exists) {
                    if ($_url_exists->has($_i) == FALSE) {
                        $_response['add'][] = $_i;
                    } else {
                        $_response['excluded'][] = $_i;
                    }
                })->values();
            }
        }

        return $_response;
    }
}

if (!function_exists('variable')) {
    function variable($key, $variables = NULL)
    {
        $_variable = new Variable();
        $_response = $_variable->_load($key);
        if (is_array($variables)) {
            $_variables = [];
            foreach ($variables as $_variable_key => $_variable_value) if (is_string($_variable_key)) $_variables["@{$_variable_key}"] = $_variable_value;
            if ($_response && $_variables) $_response = strtr($_response, $_variables);
        }

        return $_response;
    }
}

if (!function_exists('short_code')) {
    function short_code($_content, $entity = NULL)
    {
        $_variables = NULL;
        preg_match_all('|\[\:(.*?)\]|xs', $_content, $_shorts);
        if (is_object($entity)) {
            if ($_shorts && count($_shorts[0])) {
                foreach ($_shorts[0] as $_index_short => $_data_short) {
                    $_attribute_name = $_shorts[1][$_index_short];
                    $_variables[] = [
                        'code'    => $_data_short,
                        'replace' => $entity->hasAttribute($_attribute_name) ? $entity->{$_attribute_name} : NULL
                    ];
                }
            }
        } elseif (is_array($entity) && count($entity)) {
            foreach ($_shorts[0] as $_index_short => $_data_short) {
                $_attribute_name = $_shorts[1][$_index_short];
                $_variables[] = [
                    'code'    => $_data_short,
                    'replace' => isset($entity[$_attribute_name]) ? $entity[$_attribute_name] : NULL
                ];
            }
        } elseif (is_string($entity)) {
            foreach ($_shorts[0] as $_index_short => $_data_short) {
                $_variables[] = [
                    'code'    => $_data_short,
                    'replace' => $entity
                ];
            }
        }
        if ($_variables) {
            foreach ($_variables as $_replace_code) {
                $_content = str_replace($_replace_code['code'], $_replace_code['replace'], $_content);
            }
        }

        return $_content;
    }
}

if (!function_exists('config_1c')) {
    function config_1c($key = NULL, $data = NULL)
    {
        $_file_path = __DIR__ . "/../../storage/1C";
        $_1C_file = file($_file_path);
        if (!isset($_1C_file[0])) return FALSE;
        $_response = json_decode($_1C_file[0]);
        if (!is_null($data)) {
            $_response->{$key} = $data;
            file_put_contents($_file_path, json_encode($_response));

            return $_response;
        } else {
            if ($key) return $_response->{$key};

            return $_response;
        }
    }
}

if (!function_exists('dd_code')) {
    function dd_code($data = NULL, $key = 'code')
    {
        if (request()->has($key)) dd($data);
    }
}
