<?php

namespace App\Library;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ConfigFileSave
{

    public static function set($file_name, $values, $rebuild = FALSE)
    {
        $_data = $rebuild ? [] : config($file_name);
        foreach ($values as $_key => $_value) {
            $_parts = explode('.', $_key);
            $_element = &$_data;
            foreach ($_parts as $_part) $_element = &$_element[$_part];
            if (is_bool($_value)) {
                $_element = (bool)$_value;
            } elseif (is_integer($_value)) {
                $_element = (int)$_value;
            } elseif (is_float($_value)) {
                $_element = (float)$_value;
            } elseif (is_array($_value)) {
                $_element = $_value;
            } else {
                $_element = (string)$_value;
            }
        }
        $_code = "<?php \r\n return " . var_export($_data, TRUE) . ';';
        Storage::disk('config')->put("{$file_name}.php", $_code);
        Artisan::call('config:clear');

        return $_data;
    }

}
