let dedug = true;
let ajaxLoad = window.Laravel.ajaxLoad;
let activatedSubmitButton = false;

//ajax callback
function command_addClass($options) {
    if (dedug) console.log('call function command_addClass');
    jQuery($options.target).addClass($options.data);
}

function command_removeClass($options) {
    if (dedug) console.log('call function command_removeClass');
    jQuery($options.target).removeClass($options.data);
}

function command_val($options) {
    if (dedug) console.log('call function command_val');
    jQuery($options.target).val($options.data);
}

function command_data($options) {
    if (dedug) console.log('call function command_data');
    jQuery($options.target).data($options.attr, $options.data);
}

function command_attr($options) {
    if (dedug) console.log('call function command_attr');
    jQuery($options.target).attr($options.attr, $options.data);
}

function command_removeAttr($options) {
    if (dedug) console.log('call function command_removeAttr');
    jQuery($options.target).removeAttr($options.attr);
}

function command_html($options) {
    if (dedug) console.log('call function command_html');
    jQuery($options.target).html($options.data)
}

function command_text($options) {
    if (dedug) console.log('call function command_text');
    jQuery($options.target).text($options.data)
}

function command_replaceWith($options) {
    if (dedug) console.log('call function command_replaceWith');
    jQuery($options.target).replaceWith($options.data)
}

function command_append($options) {
    if (dedug) console.log('call function command_append');
    jQuery($options.target).append($options.html);
}

function command_prepend($options) {
    if (dedug) console.log('call function command_prepend');
    jQuery($options.target).prepend($options.html);
}

function command_clearForm($options) {
    if (dedug) console.log('call function command_clearForm');
    jQuery($options.target).find('input[type="text"], input[type="email"], input[type="password"], input[type="file"], textarea').val('');
    jQuery($options.target).find('input[type="checkbox"]').prop('checked', false);
    jQuery($options.target).find('select').find('option').prop('selected', false)
}

function command_slideUp($options) {
    if (dedug) console.log('call function command_slideUp');
    jQuery($options.target).slideUp(($options.duration || 500), function () {
        if ($options.eval != 'undefined') eval($options.eval);
    });
}

function command_slideDown($options) {
    if (dedug) console.log('call function command_slideDown');
    jQuery($options.target).command_slideDown(($options.duration || 500), function () {
        if ($options.eval != 'undefined') eval($options.eval);
    });
}

function command_trigger($options) {
    if (dedug) console.log('call function command_trigger');
    jQuery($options.target).trigger($options.event);
}

function command_remove($options) {
    if (dedug) console.log('call function command_remove');
    jQuery($options.target).remove();
}

function command_eval($options) {
    if (dedug) console.log('call function command_eval');
    eval($options.data);
}

function command_UK_modal($options) {
    if (dedug) console.log('call function command_UKmodal');
    $options.bgClose = $options.bgClose == undefined ? true : $options.bgClose;
    $options.clsPage = 'uk-modal-page view-ajax-modal';
    let $_id = $options.id == undefined ? 'ajax-modal' : $options.id;
    let $_class = $options.class == undefined ? '' : ' ' + $options.class;
    let $d = UIkit.modal(("<div class=\"uk-modal\" id=\"" + $_id + "\"><div class=\"uk-modal-dialog" + $_class + "\">" + $options.content + "</div></div>"), $options);
    $d.show();
    jQuery('body').delegate('#' + $_id, 'hidden', function (event) {
        if (event.target === event.currentTarget) $d.$destroy(true)
    })
}

function command_UK_notification($options) {
    if (dedug) console.log('call function command_UKnotification');
    $options.position = $options.position || 'top-center';
    if ($options.text) UIkit.notification($options.text, {status: $options.status, pos: $options.position})
}

function command_UK_modalClose($options) {
    if (dedug) console.log('call function command_UKmodalClose');
    $options.target = $options.target || '#ajax-modal';
    UIkit.modal($options.target).hide()
}

function command_UK_modalOpen($options) {
    if (dedug) console.log('call function command_UKmodalOpen');
    UIkit.modal($options.target).show()
}

function command_analyticsGtag($options) {
    if (typeof(gtag) === 'function') gtag('event', ($options.event || 'COMPLETION_SEND'), {
        event_category: ($options.category || null),
        event_action: ($options.event_action || 'SEND_FORM')
    });
    return true
}

function command_analyticsFbq($options) {
    if (typeof(fbq) === 'function') fbq('track', ($options.event || 'SEND_FORM'));
    return true
}

function command_eCommerce($options) {

}

function command_changeUrl($options) {
    if (dedug) console.log('call function command_change_url');
    history.pushState(null, null, $options.url)
}

function command_changeTitle($options) {
    if (dedug) console.log('call function command_change_url');
    document.title = $options.title
}

function command_redirect($options) {
    if (dedug) console.log('call function command_redirect');
    setTimeout(function () {
        window.location.href = $options.url
    }, ($options.time || 0))
}

function command_reload($options) {
    if (dedug) console.log('call function command_redirect');
    jQuery('body').addClass('reload-page');
    setTimeout(function () {
        location.reload()
    }, ($options.time || 0))
}

/**
 * Use
 */
function command_select2() {
    if (typeof(useSelect2) === 'function') useSelect2(jQuery);
}

function command_ckEditor() {
    if (typeof(useCkEditor) === 'function') useCkEditor(jQuery);
}

function command_fieldUpload() {
    if (typeof(useFieldUpload) === 'function') useFieldUpload(jQuery);
}

function command_easyAutocomplete() {
    if (typeof(useEasyAutocomplete) === 'function') useEasyAutocomplete(jQuery);
}

function command_сodeMirror() {
    if (typeof(useCodeMirror) === 'function') useCodeMirror(jQuery);
}

function _ajax_post($this, $ajaxHref, $ajaxData, $hideLoad) {
    let $ = jQuery;
    let ajaxOptions = {
        url: $ajaxHref,
        method: 'POST',
        data: $ajaxData,
        beforeSend: function () {
            ajaxLoad = true;
            $('body').addClass('ajax-load');
            $this.attr('disabled', 'disabled').addClass('load uk-disabled');
            if ($hideLoad) {
                $('body').addClass('ajax-not-visible-load');
            } else {
                $this.append('<div class="uk-ajax-spinner"><div uk-spinner></div></div>');
            }
        },
        success: function ($result) {
            ajaxLoad = false;
            $('body').removeClass('ajax-load ajax-not-visible-load');
            $this.removeAttr('disabled').removeClass('load uk-disabled');
            if (!$hideLoad) {
                $this.find('.uk-ajax-spinner').remove();
            }
        },
        error: function ($result) {
            ajaxLoad = false;
            $('body').removeClass('ajax-load uk-disabled ajax-not-visible-load');
            $this.removeAttr('disabled').removeClass('load uk-disabled');
            if (!$hideLoad) {
                $this.find('.uk-ajax-spinner').remove();
            }
        }
    };
    if ($ajaxData instanceof FormData) {
        ajaxOptions.enctype = 'multipart/form-data';
        ajaxOptions.processData = false;
        ajaxOptions.contentType = false;
        ajaxOptions.cache = false;
    }
    $.ajax(ajaxOptions);
}

(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'LOCALE-CODE': window.Laravel.locale,
            'LOCATION-CODE': '',
            'DEVICE': window.Laravel.device
        }
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (xhr.status == 200 && typeof xhr.responseJSON !== 'undefined' && typeof xhr.responseJSON.commands !== 'undefined') {
            if (dedug) console.log(xhr.responseJSON);
            if (xhr.responseJSON.commands !== null) {
                for (var $i = 0; $i < xhr.responseJSON.commands.length; ++$i) {
                    var command = xhr.responseJSON.commands[$i];
                    if (window['command_' + command.command] != undefined) window['command_' + command.command](command.options);
                }
            }
        }
    });
    $('body').delegate('.use-ajax', 'click touch', function (event) {
        let th = $(this);
        let skipAjaxLoad = th.data('skip-load') != undefined ? 1 : 0;
        if (ajaxLoad === false || skipAjaxLoad) {
            let elTagName = th.get(0).tagName, ajaxHref = '', ajaxData = '';
            if (elTagName == 'A' && !th.hasClass('load')) {
                event.preventDefault();
                event.stopPropagation();
                ajaxHref = th.data('path') != undefined && th.data('path') ? th.data('path') : th.attr('href');
                ajaxData = th.data();
                if (dedug) console.log('call use-ajax command. trigger A');
            } else if (elTagName == 'BUTTON' && th.attr('type') == 'button') {
                event.preventDefault();
                event.stopPropagation();
                ajaxHref = th.data('path');
                ajaxData = th.data();
                if (dedug) console.log('call use-ajax command. trigger BUTTON[TYPE=BUTTON]');
            } else if (elTagName == 'INPUT' && (th.attr('type') == 'checkbox' || th.attr('type') == 'radio')) {
                ajaxHref = th.data('path');
                ajaxData = th.data();
                ajaxData.option = th.val();
                if (th.attr('type') == 'checkbox') {
                    ajaxData.state = th.prop('checked');
                }
                if (dedug) console.log('call use-ajax command. trigger INPUT[TYPE=CHECKBOX] || INPUT[TYPE=RADIO]');
            }
            setTimeout(function () {
                let hideLoad = th.data('hide-load') != undefined ? true : false;
                if (ajaxHref && ajaxData) _ajax_post(th, ajaxHref, ajaxData, hideLoad)
            }, 200)
        } else {
            if (dedug) console.log('abort call use-ajax command. ajaxLoad = TRUE');
        }
    });
    $('body').delegate('button[type=submit], input[type=submit]', 'click touch', function (event) {
        activatedSubmitButton = $(this);
    });
    $('body').delegate('.use-ajax', 'submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (ajaxLoad === false) {
            let $this = $(this),
                ajaxHref = $this.attr('action'),
                ajaxLoad = activatedSubmitButton ? activatedSubmitButton : $this,
                hideLoad = ajaxLoad.data('hide-load') != undefined ? true : false;
            activatedSubmitButton = false;
            let ajaxData = new FormData($this[0]);
            setTimeout(function () {
                if (ajaxHref && ajaxData) _ajax_post(ajaxLoad, ajaxHref, ajaxData, hideLoad);
            }, 300);
        }
    });
    $('body').delegate('select.use-ajax', 'change', function (event) {
        event.preventDefault();
        event.stopPropagation();
        let th = $(this);
        let skipAjaxLoad = th.data('skip-load') != undefined ? 1 : 0;
        if (ajaxLoad === false || skipAjaxLoad) {
            let ajaxHref = '', ajaxData = '';
            let selectedOption = th.find('option:selected');
            ajaxHref = th.data('path');
            if (th.hasClass('uk-select2')) {
                th.on('select2:select', function (e) {
                    ajaxData = {};
                    ajaxData.option = th.val()
                })
            } else if (th.val()) {
                ajaxData = th.data();
                ajaxData.option = th.val()
            }
            if (Object.keys(selectedOption.data()).length > 0) {
                for (var key in selectedOption.data()) ajaxData[key] = selectedOption.data(key);
            }
            setTimeout(function () {
                let hideLoad = th.data('hide-load') != undefined ? true : false;
                if (ajaxHref && ajaxData) _ajax_post(th, ajaxHref, ajaxData, hideLoad)
            }, 200)
        } else {
            if (dedug) console.log('abort call use-ajax command. ajaxLoad = TRUE');
        }
    })
})(jQuery);