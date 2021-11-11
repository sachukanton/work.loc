// function useGetDataByPhone($) {
//     var th = this;
//     var as = false;
//     var t;
//     var f = $('#form-checkout-order-phone');
//     this.ajax_post_data_by_phone = function (d) {
//         as = $.ajax({
//             url: '/ajax/get-data-by-phone',
//             method: 'POST',
//             data: d,
//             beforeSend: function () {
//                 as = true;
//                 $('body').addClass('ajax-load');
//                 $('#basket-inside-items').addClass('load');
//                 f.attr('disabled', 'disabled').addClass('load')
//             },
//             success: function (r) {
//                 as = false;
//                 $('body').removeClass('ajax-load');
//                 f.removeAttr('disabled').removeClass('load');
//                 if (r) {
//                     for (var $i = 0; $i < r.length; ++$i) {
//                         command_action(r[$i])
//                     }
//                 }
//             },
//             error: function (r) {
//                 as = false;
//                 $('body').removeClass('ajax-load');
//                 f.removeAttr('disabled').removeClass('load')
//             }
//         })
//     };
//     if (f.length) {
//         var im = new Inputmask({
//             mask: '+380 99 999 99 99',
//             oncomplete: function (e) {
//                 t = setTimeout(function () {
//                     th.ajax_post_data_by_phone({phone: e.target.value})
//                 }, 500)
//             }, onKeyDown: function (event, buffer, caretPos, opts) {
//                 if (as && as.abort) as.abort();
//                 if (t) clearTimeout(t);
//                 f.removeAttr('disabled').removeClass('load');
//             }
//         });
//         im.mask(f);
//     }
// }

(function ($) {
    // useGetDataByPhone($);
    $('body').delegate('button[name=certificate]', 'click touch', function () {
        var d = {
            certificate: $('#form-checkout-order-certificate').val(),
            state: $(this).hasClass('certificate-used'),
            delivery: $('#form-checkout-order-delivery-method option:selected').val(),
        };
        _ajax_post($(this), '/ajax/certificate', d);
    });
})(jQuery)
