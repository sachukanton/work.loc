window.ajaxLoad = false;
window.timeOutAjax;

(function ($) {
    $(document).ready(function () {
        $('body').delegate('#search-container form input[type=text]', 'keyup input', function (event) {
            event.preventDefault();
            let th = $(this);
            clearTimeout(window.timeOutAjax);
            window.timeOutAjax = setTimeout(function () {
                _search_post_data(th);
            }, 1000);

        });
        $('body').delegate('#search-container select', 'change', function (event) {
            let th = $('#search-container form input[type=text]');
            _search_post_data(th);
        });
        $('body').delegate('#search-container form', 'submit', function (event) {
            let th = $(this);
            let inp = th.find('input[type=text]');
            if (!inp.val()) {
                event.preventDefault();
                event.stopPropagation();
                inp.focus();
            }
        });
    });
})(jQuery);

function _search_post_data(th) {
    let box = $('#search-container .uk-search-results-items');
    let cat = $('#form-field-category').val();
    box.html('');
    if (th.val().length >= 3) {
        let _data = {
            string: th.val(),
            category: cat == undefined ? 'all' : cat
        };
        if (window.ajaxLoad === false) {
            $.ajax({
                url: th.data('path'),
                method: "POST",
                data: _data,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'LOCALE-CODE': window.Laravel.locale,
                    'LOCATION-CODE': window.Laravel.location
                },
                beforeSend: function () {
                    window.ajaxLoad = true;
                    $('body').addClass('ajax-load');
                    th.attr('disabled', 'disabled').addClass('load');
                    box.html('');
                },
                success: function (data) {
                    window.ajaxLoad = false;
                    $('body').removeClass('ajax-load');
                    th.removeAttr('disabled').removeClass('load').focus();
                    box.html(data);
                    $('#search-container').addClass('active');
                },
                error: function (res) {
                    window.ajaxLoad = false;
                    $('body').removeClass('ajax-load');
                    th.removeAttr('disabled').removeClass('load');
                    box.html('');
                }
            });
        }
    } else {
        box.html('');
        $('#search-container').removeClass('active');
    }
    box.on('touch click', '.result-maybe', function (event) {
        event.preventDefault();
        th.val($(this).text()).trigger('keyup');
    });
    $(document).on('touch click', function (event) {
        if (!$(event.target).closest("#search-container").length) {
            box.html('');
            $('#search-container').removeClass('active');
        }
    });
}