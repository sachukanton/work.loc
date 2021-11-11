let $ajaxDelivery = false;
let $ajaxRecount = false;
let $timeOutCheckoutRecount = false;

function obj_exists($object) {
    return $object.length ? true : false;
}

function useCounterBox($) {
    if ($('.uk-input-number-counter-box').length) {
        $('.uk-input-number-counter-box').each(function () {
            let box = $(this);
            if (!box.hasClass('load')) {
                box.addClass('load');
                let bI = box.find('button[name=increment]');
                let bD = box.find('button[name=decrement]');
                let i = box.find('input[name=count]');
                let max = parseInt(i.attr('max'));
                let def = parseInt(i.data('default'));
                let step = parseInt(i.attr('step'));
                if (!max) {
                    max = 1000000;
                }
                bI.on('click', function (event) {
                    event.preventDefault();
                    let v = parseInt(i.val());
                    if (!v) v = 0;
                    v += step;
                    if (v >= max) {
                        bI.prop('disabled', true);
                        i.val(max).trigger('change');
                    } else {
                        i.val(v).trigger('change');
                    }
                    bD.prop('disabled', false);
                });
                bD.on('click', function (event) {
                    event.preventDefault();
                    let v = parseInt(i.val());
                    if (!v) v = 0;
                    v -= step;
                    if (v <= 1) {
                        bD.prop('disabled', true);
                        i.val(1).trigger('change');
                    } else {
                        i.val(v).trigger('change');
                    }
                    bI.prop('disabled', false);
                });
                i.on('keyup input', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    let v = parseInt(i.val());
                    if (!v) v = 1;
                    bI.prop('disabled', false);
                    bD.prop('disabled', false);
                    if (v == 1 && max == 1) {
                        bD.prop('disabled', true);
                        bI.prop('disabled', true);
                    } else if (v == 1) {
                        bD.prop('disabled', true);
                        bI.prop('disabled', false);
                    } else if (v >= max) {
                        v = max;
                        bI.prop('disabled', true);
                        bD.prop('disabled', false);
                    }
                    i.val(v).trigger('change');
                });
                i.change(function () {
                    let fCallback = $(this).data('callback');
                    if (fCallback != undefined) window[fCallback]($);
                });
            }
        });
    }
}

// function usePhoneMask($) {
//     if ($('input.uk-phoneMask, input[name=phone]').length) $('input.uk-phone-mask, input[name=phone]').inputmask('+38 (099) 999 99 99');
// }

function recountBasketProducts($) {
    let box = $('#form-checkout-order-products');
    clearTimeout($timeOutCheckoutRecount);
    $timeOutCheckoutRecount = setTimeout(function () {
        let basket = {};
        box.find('input[name=count]').each(function (even) {
            let e = $(this).data('e');
            let quantity = $(this).val();
            basket[e] = parseInt(quantity);
        });
        if ($ajaxRecount) $ajaxRecount.abort();
        $ajaxRecount = _ajax_post(box, '/ajax/recount-products', {
            items: basket,
            delivery_method: $('input[name="delivery_method"]:checked').val(),
        });
    }, 300);
}

function afterLoad($) {
    setTimeout(function () {
        useCounterBox($)
        // usePhoneMask($)
    }, 500)
}

(function ($) {
    $(document).ajaxComplete(function (event, request, settings) {
        afterLoad($)
    });

    $(document).ready(function () {
        afterLoad($);

        $('body').delegate('.shop-product-buy-button', 'click touch', function (event) {
            event.preventDefault();
            event.stopPropagation();
            let button = $(this);
            let box = button.parents('.shop-product-buy-box');
            if (box.length) {
                let count = parseInt(box.find('input[name=count]').val());
                let t = button.data('type');
                if (!count) count = 1;
                _ajax_post(button, button.data('path'), {quantity: count, type: t});
            }
        });

        $('a').each(function (event) {
            let l = $(this);
            let lh = l.attr('href');
            let ph = window.location.href.split('#')[0];
            if (lh != undefined) {
                if (lh[0] == '#') {
                    let hash = lh.substring(1, lh.length);
                    if ($('a[name="' + hash + '"]').length) {
                        l.attr('href', ph + lh);
                    }
                }
            }
        });
    });

    $('body').delegate('#uk-items-list-filter .filter-title i', 'click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let b = $('#uk-items-list-filter');
        if (b.hasClass('open')) {
            b.removeClass('open');
        } else {
            b.addClass('open');
        }
    });

    $('body').delegate('#form-checkout-order-products .trash', 'click touch', function (event) {
        event.preventDefault()
        event.stopPropagation()
        let b = $(this)
        if (window.ajaxCheckout) window.ajaxCheckout.abort()
        window.ajaxCheckout = _ajax_post(b, b.attr('href'), {
            page: 1,
            e: b.data('e'),
            delivery_method: $('input[name="delivery_method"]:checked').val(),
        }, 1)
    })
})(jQuery);

