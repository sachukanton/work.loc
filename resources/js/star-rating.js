;(function ($) {
    $.fn.rating = function (callback) {

        callback = callback || function () {
        };

        // each for all item
        this.each(function (i, v) {

            $(v).data('rating', {callback: callback})
                .bind('init.rating', $.fn.rating.init)
                .bind('set.rating', $.fn.rating.set)
                .bind('hover.rating', $.fn.rating.hover)
                .trigger('init.rating');
        });
    };

    $.extend($.fn.rating, {
        init: function (e) {
            var el = $(this),
                list = '',
                isChecked = null,
                childs = el.children(),
                i = 0,
                entity = $(this),
                l = childs.length;

            for (; i < l; i++) {
                let item = $(childs[i]).find('input[type=radio]');
                list = list + '<span uk-icon="icon: stargrade" class="star" data-star="' + item.val() + '" title="' + item.next().text() + '"></span>';
                if (item.is(':checked')) {
                    isChecked = item.val();
                }
            }

            childs.hide();

            el.append('<div class="uk-rateStars-box-items">' + list + '</div>')
                .trigger('set.rating', isChecked);

            $('span', el).bind('click', $.fn.rating.click);
            el.trigger('hover.rating');
        },
        set: function (e, val) {
            var el = $(this),
                item = $('span.star', el),
                input = undefined;

            if (val) {
                item.removeClass('star-checked');

                input = item.filter(function (i) {
                    if ($(this).data('star') == val) return $(this);
                    else return false;
                });

                input
                    .addClass('star-checked')
                    .prevAll()
                    .addClass('star-checked');
            }

            return;
        },
        hover: function (e) {
            var el = $(this),
                stars = $('span.star', el);

            stars.bind('mouseenter', function (e) {
                // add tmp class when mouse enter
                $(this)
                    .addClass('star-before')
                    .prevAll()
                    .addClass('star-before');

                $(this).nextAll()
                    .addClass('star-after');
            });

            stars.bind('mouseleave', function (e) {
                stars.each(function () {
                    $(this).removeClass('star-before star-after')
                });
            });
        },
        click: function (e) {
            e.preventDefault();
            let el = $(e.target).parents('.uk-icon.star'),
                entity = el.parents('.uk-rateStars'),
                rate = el.data('star'),
                matchInput;

            entity.find('input[type=radio]').each(function () {
                if ($(this).val() == rate) matchInput = $(this);
            });

            matchInput
                .prop('checked', true)
                .siblings('input').prop('checked', false);

            entity.trigger('set.rating', matchInput.val())
                .data('rating').callback(rate, e);
        }
    });

})(jQuery);
