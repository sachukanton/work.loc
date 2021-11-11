<?php

    $GLOBALS['wrap'] = NULL;

    define('DEFAULT_LOCALE', config('app.locale'));
    define('USE_MULTI_LANGUAGE', config('os_seo.use.multi_language'));
    define('DEFAULT_CURRENCY', config('os_currencies.default_currency'));
    define('USE_MULTI_CURRENCIES', config('os_currencies.multi_currency'));
    define('REMEMBER_LIFETIME', 3600);
