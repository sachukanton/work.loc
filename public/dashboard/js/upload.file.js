(function ($) {
    $('body').delegate('.uk-form-controls-file .uk-file-remove-button', 'click', function (event) {
        event.preventDefault();
        var $this = $(this),
            fieldCard = $this.parents('.uk-form-controls-file');
        $this.parents('.file').remove();
        if (fieldCard.hasClass('uk-one-file')) fieldCard.removeClass('loaded-file');
    });
})(jQuery);

function useFieldUpload($) {
    $('.js-upload').each(function () {
        var boxUpload = $(this);
        var fieldUpload = $(this).find('.file-upload-field');
        if (!fieldUpload.hasClass('applied')) {
            var fieldCard = fieldUpload.parents('.uk-form-controls-file');
            var fileView = fieldCard.data('view');
            var filePreview = fileView == 'gallery' ? fieldCard.find('.uk-preview > div') : fieldCard.find('.uk-preview');
            var optionsUpload = {
                url: fieldUpload.data('url'),
                allow: fieldUpload.data('allow'),
                multiple: fieldUpload.data('multiple'),
                type: 'post',
                name: 'file',
                params: {
                    field: fieldUpload.data('field'),
                    view: fieldUpload.data('view')
                },
                beforeSend: function (e) {
                    e.headers = {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'LOCALE-CODE': window.Laravel.locale,
                        'LOCATION-CODE': window.Laravel.location
                    };
                    fieldCard.addClass('load');
                },
                loadStart: function (e) {
                },
                progress: function (e) {
                },
                loadEnd: function (e) {
                    if (e.currentTarget.status != 200) alert('Error - ' + e.currentTarget.status);
                    fieldCard.removeClass('load');
                },
                complete: function () {
                    fieldCard.removeClass('load');
                    var $statusResponse = arguments[0].status,
                        $textResponse = arguments[0].responseText;
                    if ($statusResponse == 200) {
                        filePreview.append($($textResponse));
                        if (fieldCard.hasClass('uk-one-file')) fieldCard.addClass('loaded-file');
                    } else {
                        UIkit.notification($textResponse, {
                            status: 'danger',
                            pos: 'bottom-right'
                        });
                    }
                },
                completeAll: function () {
                    fieldCard.removeClass('load');
                },
                fail: function () {
                    fieldCard.removeClass('load');
                    UIkit.notification('Error MIME-TYPE upload the Files', {
                        status: 'danger',
                        pos: 'bottom-right'
                    });
                }
            };
            fieldUpload.addClass('applied');
            UIkit.upload('#' + boxUpload.attr('id'), optionsUpload);
        }
    });
}