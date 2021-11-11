(function ($) {
    $('body').delegate('#form-checkout-order ul.uk-tab a', 'click touch', function () {
        var t = $(this).data('type')
        $('#form-checkout-order input[name="type"]').val(t)
    });
})(jQuery)
