const mix = require('laravel-mix');

// dashboard
// mix.scripts([
//     'resources/js/jquery-3.1.1.min.js',
//     'resources/js/uikit.min.js',
//     'resources/js/uikit-icons.min.js'
// ], 'public/js/header_part.js');
// mix.babel([
//     'resources/js/select2.min.js',
//     'resources/js/air-datepicker.min.js',
//     'resources/js/jquery.easy-autocomplete.min.js',
//     'resources/js/jquery.inputmask.bundle.min.js',
//     'resources/js/star-rating.js',
//     'resources/js/use.ajax.js',
//     'resources/js/app.js'
// ], 'public/js/footer_part.js');
// mix.babel([
//     'resources/js/CkConfigFull.js',
// ], 'public/js/ck_config_full.js');
// mix.babel([
//     'resources/js/CkConfigShort.js',
// ], 'public/js/ck_config_short.js');
// mix.babel([
//     'resources/library/codemirror/lib/codemirror.js',
//     'resources/library/codemirror/addon/selection/active-line.js',
//     'resources/library/codemirror/mode/xml/xml.js',
//     'resources/library/codemirror/mode/javascript/javascript.js',
//     'resources/library/codemirror/addon/display/fullscreen.js',
// ], 'public/js/codemirror.js');
// mix.sass('resources/sass/uikit.scss', 'public/css');
// mix.styles([
//         'resources/library/codemirror/lib/codemirror.css',
//         'resources/library/codemirror/theme/idea.css',
//         'resources/library/codemirror/addon/display/fullscreen.css',
//     ], 'public/css/codemirror.css');

// template

// mix.babel([
//     'resources/js/uikit.min.js',
//     'resources/js/template/uikit-icons.min.js'
// ], 'public/template/js/header_part.js');

// mix.babel([
//     'resources/js/jquery-3.1.1.min.js',
//     // 'resources/js/template/jquery-ui.min.js',
//     // 'resources/js/jquery.inputmask.bundle.min.js',
//     // 'resources/js/select2.min.js',
//     // 'resources/js/template/search.ajax.js',
//     // 'resources/js/template/app.js',
//     // 'public/template/js/script.js',
//     'resources/js/jquery.inputmask.bundle.min.js',
//     'resources/js/template/use.ajax.js',
// ], 'public/template/js/footer_part.js');
//
mix.babel([
    // 'resources/js/jquery.inputmask.bundle.min.js',
    // 'resources/js/select2.min.js',
    'resources/js/air-datepicker.min.js',
    // 'public/template/js/checkout_script.js',
     'resources/js/template/app.js',
    // 'public/template/js/vue.js',
], 'public/template/js/checkout_part.js');

// mix.js([
//     'resources/js/vue.js',
// ], 'public/template/js/vue.js');

// mix.js([
//     'public/template/js/script.js',
// ], 'public/template/js/scripts.js');

// mix.sass('resources/sass/uikit.scss', 'public/template/css/header_part.css');
// mix.sass('resources/sass/template/app.scss', 'public/template/css/footer_part.css');
// mix.styles('resources/sass/template/style.css', 'public/template/css/app.css');
