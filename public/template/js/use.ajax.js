window.ajaxLoad = false;
window.activatedSubmitButton = false;

cmd_addClass = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).addClass(o.data);
}

cmd_removeClass = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).removeClass(o.data);
}

cmd_toggleClass = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).toggleClass(o.data);
}

cmd_css = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).css(o.data);
}

cmd_animate = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).animate(o.data, (o.options || {}));
}

cmd_val = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).val(o.data);
}

cmd_data = (options) => {
    let o = options || {};
    if (o.target != undefined && o.attr != undefined && o.data != undefined) $(o.target).data(o.attr, o.data);
}

cmd_removeData = (options) => {
    let o = options || {};
    if (o.target != undefined && o.attr != undefined) $(o.target).removeData(o.attr);
}

cmd_attr = (options) => {
    let o = options || {};
    if (o.target != undefined && o.attr != undefined && o.data != undefined) $(o.target).attr(o.attr, o.data);
}

cmd_removeAttr = (options) => {
    let o = options || {};
    if (o.target != undefined && o.attr != undefined) $(o.target).removeAttr(o.attr);
}

cmd_html = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).html(o.data)
}

cmd_text = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).text(o.data)
}

cmd_replaceWith = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).replaceWith(o.data)
}

cmd_append = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).append(o.data);
}

cmd_prepend = (options) => {
    let o = options || {};
    if (o.target != undefined && o.data != undefined) $(o.target).prepend(o.data);
}

cmd_clearForm = (options) => {
    let o = options || {};
    if (o.target != undefined) {
        $(o.target).find('input[type="text"], input[type="email"], input[type="password"], input[type="file"], textarea').val('');
        $(o.target).find('input[type="checkbox"]').prop('checked', false);
        $(o.target).find('input[type="checkbox"].is-default').prop('checked', true);
        $(o.target).find('input[type="radio"].is-default').prop('checked', true);
        $(o.target).find('select').find('option').prop('selected', false);
    }
}

cmd_slideUp = (options) => {
    let o = options || {};
    if (o.target != undefined) {
        $(o.target).slideUp((o.duration || 500), function () {
            if (o.callback != 'undefined') eval(o.callback);
        });
    }
}

cmd_slideDown = (options) => {
    let o = options || {};
    if (o.target != undefined) {
        $(o.target).slideDown((o.duration || 500), function () {
            if (o.callback != 'undefined') eval(o.callback);
        });
    }
}

cmd_trigger = (options) => {
    let o = options || {};
    if (o.target != undefined && o.callback != undefined) $(o.target).trigger(o.callback);
}

cmd_remove = (options) => {
    let o = options || {};
    if (o.target != undefined) $(o.target).remove();
}

cmd_eval = (options) => {
    let o = options || {};
    if (o.data != undefined) eval(o.data);
}

cmd_UK_modal = (options) => {
    let o = options || {};
    o.bgClose = o.bgClose || true;
    o.clsPage = 'uk-modal-page view-ajax-modal';
    let _id = o.id || 'ajax-modal';
    let _cl = o.classDialog || '';
    let _clModal = o.classModal || '';
    // if($(`body div#ajax-modal`).length) UIkit.modal('#ajax-modal').hide();
    if (o.content != undefined) {
        let modal = UIkit.modal((`<div class="uk-modal ${_clModal}" id="${_id}"><div class="uk-modal-dialog ${_cl}">${o.content}</div></div>`), o);
        modal.show();
        $('body').delegate(`#${_id}`, 'hidden', function (event) {
            if (event.target === event.currentTarget) modal.$destroy(true);
        })
    }
}

cmd_UK_modal_close_time= (options) => {
    let o = options || {};
    o.bgClose = o.bgClose || true;
    o.clsPage = 'uk-modal-page view-ajax-modal';
    let _id = o.id || 'message-ajax-modal';
    let _cl = o.classDialog || '';
    let _clModal = o.classModal || '';
    // if($(`body div#ajax-modal`).length) UIkit.modal('#ajax-modal').hide();
    if (o.content != undefined) {
        let modal = UIkit.modal((`<div class="uk-modal ${_clModal}" id="${_id}"><div class="uk-modal-dialog ${_cl}">${o.content}</div></div>`), o);
        modal.show();
        $('body').delegate(`#${_id}`, 'hidden', function (event) {
            if (event.target === event.currentTarget) modal.$destroy(true);
        })
    }
    setTimeout(function () {
        UIkit.modal(`#${_id}`).hide();
        console.log('setTimeout');
    }, (o.time || 0))
}

cmd_UK_modalClose = (options) => {
    let o = options || {};
    if (o.target != undefined && $(`body div${o.target}`).length) {
        UIkit.modal(o.target).hide();
    } else if ($(`body div#ajax-modal`).length) {
        UIkit.modal('#ajax-modal').hide();
    }
}

cmd_UK_modalOpen = (options) => {
    let o = options || {};
    if (o.target != undefined && $(`body div${o.target}`).length) {
        UIkit.modal(o.target).show()
    }
}

cmd_UK_notification = (options) => {
    let o = options || {};
    if (o.text != undefined) {
        let option = {};
        option.status = o.status || 'primary';
        option.pos = o.pos || 'top-center';
        UIkit.notification(o.text, option)
    }
}

cmd_analyticsGtag = (options) => {
    let o = options || {};
    if (typeof(gtag) === 'function') gtag('event', (o.event || 'FORM_SENDING'), {
        event_category: (o.category || 'FORM_SUBMISSION'),
        event_action: (o.action || 'COMPLETION')
    });
}

cmd_analyticsFbq = (options) => {
    let o = options || {};
    if (typeof(fbq) === 'function') fbq('track', (o.event || 'FORM_SENDING'));
}

cmd_eCommerce = (options) => {
    let o = options || {};
}

cmd_changeUrl = (options) => {
    let o = options || {};
    if (o.url != undefined) history.pushState(null, null, o.url)
}

cmd_changeTitle = (options) => {
    let o = options || {};
    if (o.title != undefined) document.title = o.title
}

cmd_redirect = (options) => {
    let o = options || {};
    if (o.url != undefined) {
        setTimeout(function () {
            window.location.href = o.url
        }, (o.time || 0))
    }
}

cmd_reload = (options) => {
    let o = options || {};
    $('body').addClass('reload-page');
    setTimeout(function () {
        location.reload()
    }, (o.time || 0))
}

cmd_select2 = () => {
    if (typeof(useSelect2) === 'function') useSelect2($);
}

cmd_ckEditor = () => {
    if (typeof(useCkEditor) === 'function') useCkEditor($);
}

cmd_fieldUpload = () => {
    if (typeof(useFieldUpload) === 'function') useFieldUpload($);
}

cmd_easyAutocomplete = () => {
    if (typeof(useEasyAutocomplete) === 'function') useEasyAutocomplete($);
}

cmd_сodeMirror = () => {
    if (typeof(useCodeMirror) === 'function') useCodeMirror($);
}

cmd_scrollToTop = (options) => {
    let o = options || {};
    if (o.target != undefined) {
        let t = o.duration || 500;
        $('html, body').animate({
            scrollTop: $(o.target).offset().top - 100
        }, t);
    }
}

function _ajax_post(obj, ajaxHref, ajaxData, hideLoad) {
    if (window.reCaptchaValid) {
        if (ajaxData instanceof FormData) {
            ajaxData.append('captcha', window.reCaptchaValid);
        } else {
            ajaxData.captcha = window.reCaptchaValid;
        }
    }
    let ajaxOptions = {
        url: ajaxHref,
        method: 'POST',
        data: ajaxData,
        beforeSend: function () {
            window.ajaxLoad = true;
            $('body').addClass('ajax-load');
            obj.attr('disabled', 'disabled').addClass('load uk-disabled');
            if (hideLoad) {
                $('body').addClass('ajax-not-visible-load');
            } else {
                obj.append('<div class="uk-ajax-spinner"><div uk-spinner></div></div>');
            }
        },
        success: function (result, status, xhr) {
            window.ajaxLoad = false;
            $('body').removeClass('ajax-load ajax-not-visible-load');
            obj.removeAttr('disabled').removeClass('load uk-disabled');
            if (!hideLoad) obj.find('.uk-ajax-spinner').remove();
            if (result.select_commands != undefined) {
                for (let $i = 0; $i < result.select_commands.length; ++$i) {
                    let c = result.select_commands[$i];
                    if (c.command != undefined) {
                        let o = c.options || {};
                        switch (c.command) {
                            case 'toggleClass':
                                if (o.data != undefined) obj.toggleClass(o.data);
                                break;
                            case 'attr':
                                if (o.attr != undefined && o.data != undefined) obj.attr(o.attr, o.data);
                                break;
                            case 'removeAttr':
                                if (o.attr != undefined && o.data != undefined) obj.removeAttr(o.attr, o.data);
                                break;
                            case 'text':
                                if (o.data != undefined) obj.text(o.data);
                                break;
                            case 'html':
                                if (o.data != undefined) obj.html(o.data);
                                break;
                        }
                    }
                }
            }
        },
        error: function (xhr, status, error) {
            window.ajaxLoad = false;
            $('body').removeClass('ajax-load uk-disabled ajax-not-visible-load');
            obj.removeAttr('disabled').removeClass('load uk-disabled');
            cmd_UK_notification({
                text: error,
                status: 'danger'
            });
            if (!hideLoad) obj.find('.uk-ajax-spinner').remove();
        }
    };
    if (ajaxData instanceof FormData) {
        ajaxOptions.enctype = 'multipart/form-data';
        ajaxOptions.processData = false;
        ajaxOptions.contentType = false;
        ajaxOptions.cache = false;
    }
    return $.ajax(ajaxOptions);
}

(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.Laravel.csrfToken,
            'LOCALE': window.Laravel.locale,
            'LOCATION': '',
            'DEVICE': window.Laravel.device
        }
    });
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (xhr.status == 200 && typeof xhr.responseJSON !== 'undefined' && typeof xhr.responseJSON.commands !== 'undefined') {
            if (xhr.responseJSON.commands !== null) {
                for (let $i = 0; $i < xhr.responseJSON.commands.length; ++$i) {
                    let command = xhr.responseJSON.commands[$i];
                    if (window['cmd_' + command.command] != undefined) window['cmd_' + command.command](command.options);
                }
            }
        }
    });
    $('body').delegate('.use-ajax', 'click touch', function (event) {
        let th = $(this);
        let skipAjaxLoad = th.data('skip_load') != undefined ? 1 : 0;
        if (window.ajaxLoad === false || skipAjaxLoad) {
            let elTagName = th.get(0).tagName, ajaxHref = '', ajaxData = '';
            if (elTagName == 'A' && !th.hasClass('load')) {
                event.preventDefault();
                event.stopPropagation();
                let attrDisabled = th.attr('disabled');
                if (typeof attrDisabled === typeof undefined) {
                    ajaxHref = th.data('path') != undefined && th.data('path') ? th.data('path') : th.attr('href');
                    ajaxData = th.data();
                }
            } else if (elTagName == 'BUTTON' && th.attr('type') == 'button') {
                event.preventDefault();
                event.stopPropagation();
                let attrDisabled = th.attr('disabled');
                if (typeof attrDisabled === typeof undefined) {
                    ajaxHref = th.data('path');
                    ajaxData = th.data();
                }
            } else if (elTagName == 'INPUT' && (th.attr('type') == 'checkbox' || th.attr('type') == 'radio')) {
                ajaxHref = th.data('path');
                ajaxData = th.data();
                ajaxData.option = th.val();
                if (th.attr('type') == 'checkbox') {
                    ajaxData.state = th.prop('checked');
                }
            }
            setTimeout(function () {
                let hideLoad = th.data('hide_load') != undefined ? true : false;
                if (ajaxHref && ajaxData) _ajax_post(th, ajaxHref, ajaxData, hideLoad)
            }, 200)
        } else {
        }
    });
    $('body').delegate('button[type=submit], input[type=submit]', 'click touch', function (event) {
        window.activatedSubmitButton = $(this);
    });
    $('body').delegate('form.use-ajax', 'submit', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (window.ajaxLoad === false) {
            let $this = $(this),
                ajaxHref = $this.attr('action'),
                ajaxLoad = window.activatedSubmitButton ? window.activatedSubmitButton : $this,
                hideLoad = ajaxLoad.data('hide-load') != undefined ? true : false;
            window.activatedSubmitButton = false;
            let ajaxData = new FormData($this[0]);
            if (ajaxLoad.is('[name]')) ajaxData.append(ajaxLoad.attr('name'), ajaxLoad.val());
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
        if (window.ajaxLoad === false || skipAjaxLoad) {
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
                for (let key in selectedOption.data()) ajaxData[key] = selectedOption.data(key);
            }
            setTimeout(function () {
                let hideLoad = th.data('hide-load') != undefined ? true : false;
                if (ajaxHref && ajaxData) _ajax_post(th, ajaxHref, ajaxData, hideLoad)
            }, 200)
        } else {
        }
    })
})(jQuery);