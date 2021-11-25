
// function choiceImg() {
//         if (window.Laravel != undefined) {
//             this.exist = true;
//             this.image = '.front-products';
//             let $this = this;
//             let $choice_img = $(this.image);
//             if ($choice_img.length) {
//                 if ($choice_img != undefined && !sessionStorage.getItem("dismissNotice")) {
//
//
//                     console.log('Choice image');
//
//
//                     sessionStorage.setItem("dismissNotice", "Choice image");
//                 }else {
//
//                 }
//             }
//         }
//     }










function usePhoneMask($) {
    if (exist('input.phone-mask, input.uk-phoneMask')) {
        $('input.phone-mask, input.uk-phoneMask').inputmask('+38 (099) 999 99 99');
    }
}


(function ($) {
    $(document).ready(function () {
        usePhoneMask($);
        useDatePicker($);



        $("a").mouseenter(function () {
            var title = $(this).attr("title");
            $(this).attr("tmp_title", title);
            $(this).attr("title", "")
        }).mouseleave(function () {
            var title = $(this).attr("tmp_title");
            $(this).attr("title", title)
        }).click(function () {
            var title = $(this).attr("tmp_title");
            $(this).attr("title", title)
        });
        var baseH;
        $('body').one('focus.textarea', '.text-area', function (e) {
            baseH = this.scrollHeight
        }).on('input.textarea', '.text-area', function (e) {
            if (baseH < this.scrollHeight) {
                $(this).height(0).height(this.scrollHeight)
            } else {
                $(this).height(0).height(baseH)
            }
        });

        /*
        $('.stock').bind('input', function(){
           var th = $(this); 
           $.ajax({
                url: '/ajax/stock/ispromocode', 
                method: 'post',
                dataType: 'json',
                data: {code: $(this).val()},
                success: function(data){
                    //if(data.status)  
                    _ajax_post(th, '/ajax/stock/addpromocode');
                }
            });
        });
        */

        $('body').delegate('.btn-submit-promo', 'click', function () {
           var th = $("#form-checkout-order-stock");

          $.ajax({
                url: '/ajax/stock/ispromocode', 
                method: 'post',
                data: {
                    code: th.val(),
                    val: $(".btn-submit-promo").val()
                    },
                success: function(data){
                    _ajax_post(th, '/ajax/stock/addpromocode',{data:data});
                }
            });
            return false;

        });


        $('body').delegate('.icons-category', 'click', function () {
            if($(this).hasClass('open')) {
                $(this).removeClass('open');
            }else {
                $(this).addClass('open');
            }
            $('.category-menu').slideToggle();
        });

        $('body').delegate('.btn-filter-params', 'click', function () {
            if($(this).hasClass('open')) {
                $(this).removeClass('open');
            }else {
                $(this).addClass('open');
            }
        });

        // $('.icon-img-full .uk-checkbox').click(function(){
        //     if ($('.icon-img-full .uk-checkbox').is(':checked')){
        //         $('body').addClass('img-full');
        //         $(this).addClass('open');
        //
        //         $(".full-fid-href").each(function () {
        //             var a= $(".shop-product-change-images").attr('id');
        //             for(i=0;i<a.length;i++)
        //             {
        //                 var link = $('#product-'+ i + ' a.full-fid-href').attr('href');
        //                 $('#product-'+ i + ' .preview-fid img').attr("src", link);
        //             }
        //         })
        //     }else {
        //         $(this).removeClass('open');
        //         $('body').removeClass('img-full');
        //
        //         $(".full-fid-href").each(function () {
        //             var a= $(".shop-product-change-images").attr('id');
        //             for(i=0;i<a.length;i++)
        //             {
        //                 var link = $('#product-'+ i + ' a.preview-fid-href').attr('href');
        //                 $('#product-'+ i + ' .preview-fid img').attr("src", link);
        //             }
        //         })
        //     }
        //     // choiceImg();
        // });
        // $('.icon-consist-full .uk-checkbox').click(function(){
        // if ($('.icon-consist-full .uk-checkbox').is(':checked')){
        //     $('.consist').slideUp( "slow", function() {});
        // } else {
        //     $('.consist').slideDown( "slow", function() {});
        // }
        // });

        // $('body').delegate('.uk-link-consist', 'click', function () {
        //     event.preventDefault();
        //     $(this).remove();
        //     $('.last-line-consist').slideDown();
        // });
        //
        // $('body').delegate('.uk-link-related', 'click', function () {
        //     event.preventDefault();
        //     $(this).remove();
        //     $('.last-line-related').slideDown();
        // });
        // $('body').delegate('.teaser-consist', 'hover', function () {
        //     if($(this).hasClass('open')) {
        //         $(this).find('.consist-card').slideDown();
        //     }else {
        //         $(this).find('.consist-card').slideUp();
        //     }
        //
        // });

        // $(".teaser-consist").on("mouseover mouseout", function () {
        //     var $resp_consist = $('.consist-card');
        //     $('.teaser-consist').removeClass('mouse');
        //     if ($(this).hasClass('mouse')) {
        //         $(this).find('.consist-card').slideUp();
        //         $(this).removeClass('mouse');
        //         console.log('1');
        //     } else {
        //         $(this).find('.consist-card').slideDown();
        //         $(this).addClass('mouse');
        //         console.log('2');
        //     }
        // });


        // $(".icon-img-full").click(function(){
        //     var newsrc;
        //     var realsrc;
        //         newsrc= $('.view-lists-product').find('.product-1 .preview-fid img').src;
        //         realsrc= $('.view-lists-product').find('.product-1 .full-fid img').src;
        //         realsrc.attr("src", newsrc);
        //     // if($(this).attr("src")=="/images/chatbuble.png")
        //     // {
        //     //     newsrc="/images/closechat.png";
        //     //     $(this).attr("src", newsrc);
        //     // }
        //     // else
        //     // {
        //     //     newsrc="/images/chatbuble.png";
        //     //     $(this).attr("src", newsrc);
        //     // }
        // });



    });

    $(document).ajaxComplete(function (event, request, settings) {
        usePhoneMask($);

    });

})(jQuery);




function exist($target) {
    var $ = jQuery;
    return $($target).length ? true : false
}


function useDatePicker($) {
    if (exist('input.uk-datepicker')) {
        $.fn.datepicker.language['en'] = {
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            daysMin: ['Sn', 'Mn', 'Te', 'Wd', 'Th', 'Fr', 'St'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            today: 'Today',
            clear: 'Clear',
            dateFormat: 'dd.mm.yyyy',
            timeFormat: 'hh:ii',
            firstDay: 1
        };

        $.fn.datepicker.language['ru'] = {
            days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            daysShort: ['Вос', 'Пон', 'Вто', 'Сре', 'Чет', 'Пят', 'Суб'],
            daysMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
            today: 'Сегодня',
            clear: 'Очистить',
            dateFormat: 'dd.mm.yyyy',
            timeFormat: 'hh:ii',
            firstDay: 1
        };

        $.fn.datepicker.language['ua'] = {
            days: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четверг', 'П\'ятниця', 'Субота'],
            daysShort: ['Нед', 'Пон', 'Вів', 'Сер', 'Чет', 'П\'ят', 'Суб'],
            daysMin: ['Нд', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
            monthsShort: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
            today: 'Сьогодні',
            clear: 'Очистити',
            dateFormat: 'dd.mm.yyyy',
            timeFormat: 'hh:ii',
            firstDay: 1
        };
        if (document.documentElement.lang === "ua") {
            $_lang = 'ua';
        }
        if (document.documentElement.lang === "ru") {
            $_lang = 'ru';
        }
        if (document.documentElement.lang === "en") {
            $_lang = 'en';
        }

        var minDate = new Date();
        // minHours = minDate.getHours() + 1;
        // minHours = (minDate.getMinutes()>30?minDate.getHours() + 2:minDate.getHours() + 1);
        // minMinutes = minDate.setMinutes(minDate.getMinutes() + 30);
        //
        // minDate.setHours(minHours);

        minDate.setMinutes(minDate.getMinutes() + 90);


        $('input.uk-datepicker').datepicker({
            language: $_lang,
            minDate: minDate,
            timepicker: true,
            minHours: 7,
            maxHours: 22,
            useStrict: true,
            disableTouchKeyboard: true,

        })
        $("#form-checkout-order-pre-order-at").data('datepicker').selectDate(new Date(minDate));
        // var x = document.getElementById("form-checkout-order-pre-order-at");
        //  $("#form-checkout-order-pre-order-at").selectDate(new Date(2018, 3, 11));
        // var d = minDate.getDate();
        // var mth = minDate.getMonth()+1;
        // var y = minDate.getFullYear();
        // var h = minDate.getHours();
        // var m = (minDate.getMinutes()<10?'0':'') + minDate.getMinutes();
        // x.value = d + "." + mth + "." + y + " " + h + ":" + m;
    }
}



