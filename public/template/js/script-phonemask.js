
(function ($) {
    $(document).ready(function () {

        usePhoneMask($);

    });

    $(document).ajaxComplete(function (event, request, settings) {
        usePhoneMask($);
    });

})(jQuery);

function usePhoneMask($) {
    $('input.phone-mask, input.uk-phoneMask').inputmask('+38 (099) 99 99 999');
}

function exist($target) {
    var $ = jQuery;
    return $($target).length ? true : false
}



