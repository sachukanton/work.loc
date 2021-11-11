var $cookie = new Cookie();
var $ckEditorObject = {};
var $open_menu;

(function ($) {
    $(document).delegate('.uk-checkboxes-used-all input[type="checkbox"]', 'click', function (event) {
        let $checkbox = $(this);
        let $checkboxes_box = $checkbox.parents('.uk-form-controls-checkbox.uk-checkboxes-used-all');
        if($checkbox.prop('checked') && $checkbox.data('key') == 'all') {
            $checkboxes_box.find('input[type="checkbox"]').prop('checked', false);
            $checkbox.prop('checked', true);
        }else if($checkbox.prop('checked')){
            $checkboxes_box.find('input[data-key="all"]').prop('checked', false);
        }else if($checkboxes_box.find('input[data-key!="all"]:checked').length == 0){
            $checkboxes_box.find('input[data-key="all"]').prop('checked', true);
        }
    });
    
    $(document).delegate('.uk-menu-hamburger', 'click', function (event) {
        $open_menu = Number($cookie.get('open_admin_menu', 0, {path: '/'}));
        if (Laravel.device != 'pc') {
            $left_side_bar = $('.uk-left-side-bar');
            if ($left_side_bar.hasClass('uk-open')) {
                $left_side_bar.removeClass('uk-open');
            } else {
                $left_side_bar.addClass('uk-open');
            }
        } else {
            if ($open_menu) {
                $cookie.set('open_admin_menu', 0, {path: '/'});
                $('.uk-dashboard').removeClass('uk-open-menu');
            } else {
                $cookie.set('open_admin_menu', 1, {path: '/'});
                $('.uk-dashboard').addClass('uk-open-menu');
            }
        }
    });

    $('body').delegate('.uk-button-delete-entity', 'click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var $button = $(this);
        var $item_id = $button.data('item');
        var $form_delete = $('#form-delete-' + $item_id + '-object');
        var $form_delete_data = $form_delete.serialize();
        if ($button.hasClass('use-ajax')) {
            $.ajax({
                url: $form_delete.attr('action'),
                method: 'DELETE',
                data: $form_delete_data,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                success: function ($result) {
                    if ($result) {
                        for (var $i = 0; $i < $result.length; ++$i) {
                            command_action($result[$i]);
                        }
                    }
                },
            });
        } else {
            if ($form_delete.length && $form_delete.get(0).tagName == 'FORM') $form_delete.submit();
        }
    });

    $('body').delegate('.button-manager-in-basket', 'click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let $button = $(this);
        let $drug_row = $button.parents('.manager-prices-table-row');
        let $drug_count = parseInt($drug_row.find('input[type="number"]').val());
        let $ajaxHref = '/oleus/action-drug-on-basket';
        let $ajaxData = $button.data();
        $ajaxData.count = $drug_count ? $drug_count : 1;
        _ajax_post($button, $ajaxHref, $ajaxData);
    });

    $('body').delegate('#catalog-search-form', 'submit', function (event) {
        $('#load-update-order-lists').addClass('load');
        $('#catalog-search-result').css({opacity: .5});
    });

    $('body').delegate('.showing-network-availability', 'click', function (event) {
        let $id = $(this).data('id');
        $('.box-prices-table .prices-table:not(.prices-table-' + $id + ')').attr('hidden', 'hidden');
    });

    $(document).ajaxComplete(function (event, request, settings) {
        after_load();
    });

    $(document).ready(function () {
        after_load();
    });

    function after_load() {
        useSelect2($);
        usePhoneMask($);
        useDatePicker($);
        useFieldUpload($);
        useCkEditor($);
    }

    useEasyAutocomplete($);
    useCodeMirror($);
})(jQuery);

function obj_exists($object) {
    return $object.length ? true : false;
}

function Cookie() {
    this.get = function (name, default_value) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : default_value;
    };

    this.set = function (name, value, options) {
        var options = options || {};
        var value = encodeURIComponent(value);
        var updatedCookie = name + "=" + value;
        var expires = options.expires;
        if (typeof expires == "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }
        for (var propName in options) {
            updatedCookie += ";" + propName;
            var propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }
        console.log(updatedCookie);
        document.cookie = updatedCookie;
    };

    this.delete = function (name) {
        this.set(name, '', {expires: -1});
    };
}

function useSelect2($) {
    $('select.uk-select2').select2({
        width: '100%'
    });
}

function usePhoneMask($) {
    $('input.uk-phone-mask').inputmask('+38(999) 999-9999');
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
                CKEDITOR.config.customConfig = '/dashboard/js/CkConfigShort.js';
                $optionsEditor = {
                    height: 150
                };
            } else {
                CKEDITOR.config.customConfig = '/dashboard/js/CkConfigFull.js';
                $optionsEditor = {
                    height: 250
                };
            }
            if (!$('#cke_' + $idField).length) {
                $ckEditorObject[$idField] = CKEDITOR.replace($idField, $optionsEditor);
                $ckEditorObject[$idField].on('change', function (ck) {
                    $('#' + $idField).val(ck.editor.getData());
                });
                // CKEDITOR.config.contentsCss = '/dashboard/css/uikit.min.css';
                CKEDITOR.config.startupOutlineBlocks = true;
            }
        }
    });
}

function useCodeMirror($) {
    $('textarea.uk-codeMirror').each(function () {
        var $id = $(this).attr('id');
        CodeMirror.fromTextArea(document.getElementById($id), {
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
    });
}

function useEasyAutocomplete($) {
    $('input.uk-autocomplete').each(function () {
        var input = $(this),
            parent = input.parents('.uk-form-controls-autocomplete'),
            inputValue = parent.find('input[type="hidden"]');
        if (input.data('url')) {
            input.easyAutocomplete({
                url: input.data('url'),
                ajaxSettings: {
                    dataType: 'json',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
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
                        return item.view !== undefined ? (value + ' - <span style="font-size: 0.9em; color: #aaa; font-style: italic;">' + item.view + '</span>') : value;
                    }
                },
                list: {
                    onChooseEvent: function () {
                        var item = input.getSelectedItemData();
                        inputValue.val(item.data).trigger("change");
                    },
                    onLoadEvent: function () {
                        inputValue.val('').trigger("change");
                    },
                    maxNumberOfElements: 10,
                    match: {
                        enabled: true
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