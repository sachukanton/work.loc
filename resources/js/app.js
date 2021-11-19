let $cookie = new Cookie();
let $ckEditorObject = new Array();
let $codeMirrorObject = new Array();
let $open_menu;
let $ajaxDelivery = false;
let $ajaxRecount = false;
let $timeOutCheckoutRecount = false;

function obj_exists($object) {
    return $object.length ? true : false;
}

function Cookie() {
    this.get = function (name, default_value) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : default_value;
    };

    this.set = function (name, val, opt) {
        let options = opt || {};
        let value = encodeURIComponent(val);
        let updatedCookie = name + "=" + value;
        let expires = options.expires;
        if (typeof expires == "number" && expires) {
            let d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }
        for (let propName in options) {
            updatedCookie += ";" + propName;
            let propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        document.cookie = updatedCookie;
    };

    this.delete = function (name) {
        this.set(name, '', {expires: -1});
    };
}

function useSelect2($) {
    $('select.uk-select2').select2({
        width: '100%',
        minimumResultsForSearch: 10
    });
}

function useFieldUpload($) {
    $('.js-upload').each(function () {
        let $boxUpload = $(this);
        let $fieldUpload = $(this).find('.file-upload-field');
        if (!$fieldUpload.hasClass('applied')) {
            let $fieldCard = $fieldUpload.parents('.uk-form-controls-file');
            let $fileView = $fieldCard.data('view');
            let $filePreview = $fileView == 'gallery' ? $fieldCard.find('.uk-preview > div') : $fieldCard.find('.uk-preview');
            let $settingsUpload = {
                url: $fieldUpload.data('url'),
                allow: $fieldUpload.data('allow'),
                multiple: $fieldUpload.data('multiple') ? true : false,
                type: 'POST',
                name: 'file',
                params: {
                    field: $fieldUpload.data('field'),
                    view: $fieldUpload.data('view')
                },
                beforeSend: function (e) {
                    e.headers = {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'LOCALE': window.Laravel.locale,
                        'LOCATION': '',
                        'DEVICE': window.Laravel.device
                    };
                    $fieldCard.addClass('load');
                },
                loadEnd: function (e) {
                    if (e.currentTarget.status != 200) alert('Error - ' + e.currentTarget.status);
                    $fieldCard.removeClass('load');
                },
                complete: function () {
                    let $statusResponse = arguments[0].status,
                        $textResponse = arguments[0].responseText;
                    if ($statusResponse == 200) {
                        $filePreview.append($($textResponse));
                        if ($fieldCard.hasClass('uk-one-file')) $fieldCard.addClass('loaded-file');
                    } else {
                        UIkit.notification($textResponse, {
                            status: 'danger',
                            pos: 'bottom-right'
                        });
                    }
                },
                completeAll: function () {
                    $fieldCard.removeClass('load');
                },
                fail: function () {
                    $fieldCard.removeClass('load');
                    UIkit.notification(Laravel.translate.upload_file_mime_type, {
                        status: 'danger',
                        pos: 'bottom-right'
                    });
                }
            };
            $fieldUpload.addClass('applied');
            UIkit.upload('#' + $boxUpload.attr('id'), $settingsUpload);
        }
    });
}

function useDatePicker($) {
    $('input.uk-datepicker').datepicker({});
}

function useCkEditor($) {
    $('.uk-ckEditor').each(function () {
        var $idField = null,
            $optionsEditor = {};
        if ($idField = $(this).attr('id')) {
            if ($(this).hasClass('editor-short')) {
                $optionsEditor = {
                    height: 150,
                    customConfig: '/js/ck_config_short.js'
                };
            } else {
                $optionsEditor = {
                    height: 250,
                    customConfig: '/js/ck_config_full.js'
                };
            }
            if (!$('#cke_' + $idField).length) {
                $ckEditorObject[$idField] = CKEDITOR.replace($idField, $optionsEditor);
                $ckEditorObject[$idField].on('change', function (ck) {
                    $('#' + $idField).val(ck.editor.getData());
                });
                CKEDITOR.config.startupOutlineBlocks = true;
            }
        }
    });
}

function useEasyAutocomplete($) {
    $("input.uk-autocomplete:not(.load)").each(function () {
        var input = $(this),
            parent = input.parents('.uk-form-controls-autocomplete'),
            inputValue = parent.find('input[type="hidden"]'),
            highlight = input.data('highlight');
        if (highlight == undefined || !highlight) highlight = true;
        if (input.data('url')) {
            input.addClass('load');
            input.easyAutocomplete({
                url: input.data('url'),
                ajaxSettings: {
                    dataType: 'json',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    beforeSend: function () {
                        parent.append('<div uk-spinner></div>');
                        inputValue.val('').trigger("change");
                    },
                    data: {
                        dataType: 'json'
                    }
                },
                getValue: input.data('value'),
                requestDelay: 500,
                template: {
                    type: 'custom',
                    method: function (value, item) {
                        return item.view !== undefined && item.view ? (value + ' - <span style="font-size: 0.9em; color: #aaa; font-style: italic;">' + item.view + '</span>') : value;
                    }
                },
                list: {
                    onChooseEvent: function () {
                        var item = input.getSelectedItemData();
                        inputValue.val(item.data).trigger("change");
                    },
                    onLoadEvent: function () {
                        var item = input.getItems();
                        var selectVal = input.val();
                        inputValue.val('').trigger("change");
                        parent.find('.uk-spinner').remove();
                        if (!item.length && selectVal != '<front>' && selectVal != '<none>') {
                            parent.find('.easy-autocomplete').append('<div class="easy-autocomplete-no-result">No result</div>');
                            setTimeout(function () {
                                parent.find('.easy-autocomplete-no-result').remove();
                            }, 3000);
                        } else {
                            parent.find('.easy-autocomplete-no-result').remove();
                        }
                    },
                    maxNumberOfElements: 10,
                    match: {
                        enabled: false
                    }
                },
                preparePostData: function (data) {
                    data.search = input.val();
                    return data;
                }
            });
        }
    });
}

function useRateStar($) {
    if ($('.uk-rateStars').length) {
        $('.uk-rateStars').each(function () {
            if (!$(this).hasClass('load')) {
                $(this).rating();
                $(this).addClass('load');
            }
        });
    }
}

function usePhoneMask($) {
    if ($('input.uk-phoneMask, input[name=phone]').length) $('input.uk-phone-mask, input[name=phone]').inputmask('+38(999) 999-9999');
};

function useCounterBox($) {
    if ($('.uk-input-number-counter-box').length) {
        $('.uk-input-number-counter-box').each(function () {
            let box = $(this);
            if (!box.hasClass('load')) {
                let bI = box.find('button[name=increment]');
                let bD = box.find('button[name=decrement]');
                let i = box.find('input[name=count]');
                let max = i.attr('max');
                let def = i.attr('default');
                if (!max) {
                    max = 1000000;
                } else {
                    max = parseInt(max);
                }
                bI.on('click', function (event) {
                    event.preventDefault();
                    let v = parseInt(i.val());
                    if (!v) v = 0;
                    v++;
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
                    v--;
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

function useMinHeight($) {
    let mainMinHeight = $(window).height();
    if (obj_exists($('header'))) mainMinHeight -= $('header').outerHeight();
    if (obj_exists($('footer'))) mainMinHeight -= $('footer').outerHeight();
    if (typeof mainMinHeight == "number") $('main').css({minHeight: mainMinHeight + 'px'});
}

function recount_basket_products($) {
    let box = $('#form-checkout-order-products');
    clearTimeout($timeOutCheckoutRecount);
    $timeOutCheckoutRecount = setTimeout(function () {
        let basket = {};
        box.find('input[name=count]').each(function (even) {
            let product = $(this).data('product');
            let quantity = $(this).val();
            basket[product] = parseInt(quantity);
        });
        if ($ajaxRecount) $ajaxRecount.abort();
        $ajaxRecount = _ajax_post(box, '/ajax/recount-products', {items: basket});
    }, 1500);
}

function useUpdateOrderLists($) {
    let $load = jQuery('#load-update-order-lists');
    _ajax_post($load, '/oleus/order-lists-update', {
        last_updated_at: window.update_order_lists_last_create_at
    });
}

(function ($) {
    $(document).delegate('.uk-checkboxes-used-all input[type="checkbox"]', 'click touch', function (event) {
        let $checkbox = $(this);
        let $checkboxes_box = $checkbox.parents('.uk-checkboxes-used-all');
        if ($checkbox.prop('checked') && $checkbox.data('key') == 'all') {
            $checkboxes_box.find('input[type="checkbox"]').prop('checked', false);
            $checkbox.prop('checked', true);
        } else if ($checkbox.prop('checked')) {
            $checkboxes_box.find('input[data-key="all"]').prop('checked', false);
        } else if ($checkboxes_box.find('input[data-key!="all"]:checked').length == 0) {
            $checkboxes_box.find('input[data-key="all"]').prop('checked', true);
        }
    });

    $('body').delegate('.uk-form-controls-file .uk-file-remove-button', 'click touch', function (event) {
        event.preventDefault();
        let $this = $(this),
            $fieldCard = $this.parents('.uk-form-controls-file');
        $this.parents('.file').remove();
        if ($fieldCard.hasClass('uk-one-file')) $fieldCard.removeClass('loaded-file');
    });

    $('body').delegate('.uk-button-delete-entity', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let $button = $(this);
        let $item_id = $button.data('item');
        let $form_delete = $('#form-delete-' + $item_id + '-object');
        let $form_delete_data = $form_delete.serialize();
        if ($button.hasClass('use-ajax')) {
            $.ajax({
                url: $form_delete.attr('action'),
                method: 'DELETE',
                data: $form_delete_data,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                success: function ($result) {
                },
            });
        } else {
            if ($form_delete.length && $form_delete.get(0).tagName == 'FORM') $form_delete.submit();
        }
    });

    $('body').delegate('.uk-button-use-code-mirror', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let $button = $(this);
        let $id = $button.data('id');
        if ($id) {
            if ($codeMirrorObject[$id] != undefined) $codeMirrorObject[$id].toTextArea();
            if (CKEDITOR.instances[$id] != undefined) {
                CKEDITOR.instances[$id].destroy();
                CKEDITOR.remove($id);
                $('button.uk-button-use-ckEditor[data-id="' + $id + '"]').removeClass('on');
                $ckEditorObject.splice($id, 1);
            }
            if ($button.hasClass('on')) {
                $button.removeClass('on');
                $codeMirrorObject.splice($id, 1);
            } else {
                $codeMirrorObject[$id] = CodeMirror.fromTextArea(document.getElementById($id), {
                    lineNumbers: true,
                    styleActiveLine: true,
                    name: 'javascript',
                    theme: 'idea',
                    extraKeys: {
                        "F11": function (cm) {
                            cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                        },
                        "Esc": function (cm) {
                            if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                        }
                    }
                });
                $button.addClass('on');
            }
        }
    });

    $('body').delegate('.uk-button-use-ckEditor', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let $button = $(this);
        let $id = $button.data('id');
        if ($id) {
            let $editor = $('#' + $id);
            if ($codeMirrorObject[$id] != undefined) {
                $codeMirrorObject[$id].toTextArea();
                $codeMirrorObject.splice($id, 1);
                $('button.uk-button-use-code-mirror[data-id="' + $id + '"]').removeClass('on');
            }
            if (CKEDITOR.instances[$id] != undefined) {
                CKEDITOR.instances[$id].destroy();
                CKEDITOR.remove($id);
            }
            if ($button.hasClass('on')) {
                $button.removeClass('on');
                $ckEditorObject.splice($id, 1);
            } else {
                if ($editor.hasClass('editor-short')) {
                    $optionsEditor = {
                        height: 150,
                        customConfig: '/js/ck_config_short.js'
                    };
                } else {
                    $optionsEditor = {
                        height: 250,
                        customConfig: '/js/ck_config_full.js'
                    };
                }
                if (!$('#cke_' + $id).length) {
                    $ckEditorObject[$id] = CKEDITOR.replace($id, $optionsEditor);
                    $ckEditorObject[$id].on('change', function (ck) {
                        $('#' + $id).val(ck.editor.getData());
                    });
                    CKEDITOR.config.startupOutlineBlocks = true;
                }
                $button.addClass('on');
            }
        }
    });

    $('body').delegate('.uk-button-save-sorting', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let button = $(this);
        let values = {};
        $('input.uk-input-sort-item').each(function ($i) {
            values[$(this).data('id')] = $(this).val();
        });
        _ajax_post(button, button.attr('href'), values);
    });

    $('body').delegate('#form-field-categories', 'change', function (event) {
        event.preventDefault();
        event.stopPropagation();
        $('#form-field-categories-selection-button').removeAttr('disabled').addClass('uk-animation-shake');
        setTimeout(function () {
            $('#form-field-categories-selection-button').removeClass('uk-animation-shake');
        }, 1500);

    });

    $('body').delegate('#form-field-categories-selection-button', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        _ajax_post($(this), $(this).data('path'), {categories: $('#form-field-categories').val()});
    });

    $('body').delegate('#shop-product-slideshow-nav li', 'click touch', function (event) {
        let ind = $(this).data('index_slide');
        UIkit.slideshow('#shop-product-slideshow').show(ind);
    });

    $('body').delegate('#shop-product-buy-button', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let button = $(this);
        let box = button.parents('#shop-product-action-box');
        if (box.length) {
            let count = parseInt(box.find('input[name=count]').val());
            if (!count) count = 1;
            _ajax_post(button, button.data('path'), {quantity: count});
        }
    });

    $('body').delegate('#form-checkout-order-delivery-method input[type=radio]', 'change', function (event) {
        let input = $(this);
        let box = $('#form-checkout-order');
        if ($ajaxDelivery) $ajaxDelivery.abort();
        $ajaxDelivery = _ajax_post(box, '/ajax/delivery-box', {method: input.val()}, false);
    });

    $('body').delegate('#form-checkout-order-products a.uk-remove-product', 'click touch', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let link = $(this);
        let box = $('#form-checkout-order-products');
        _ajax_post(box, '/ajax/remove-products', {item: link.data('product')});
    });

    $(document).ajaxComplete(function (event, request, settings) {
        after_load();
    });

    $(document).ready(function () {
        useMinHeight($);
        after_load();
    });

    $(window).resize(function () {
        useMinHeight($);
    });

    function after_load() {
        useSelect2($);
        useFieldUpload($);
        useDatePicker($);
        usePhoneMask($);
        // useFieldUpload($);
        useCkEditor($);
        useEasyAutocomplete($);
        useRateStar($);
        useCounterBox($);
    }

    if (window.update_order_lists != undefined) {
        setInterval(useUpdateOrderLists, 15000);
    }

})(jQuery);

