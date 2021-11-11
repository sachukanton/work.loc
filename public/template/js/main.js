$(document).ready(function () {

let index = -1;
$('.phone a').first().addClass('active');
$('.phone a').next().removeClass('active').fadeOut(0);
setInterval(function($items) {
  $items.eq(index).removeClass('active').fadeOut(0);
  index = (index + 1) % $items.length;
  $items.eq(index).addClass('active').fadeIn(300);
}, 4000, $('.phone a'));

var intro = new Swiper(".intro_slider", {
        spaceBetween: 5,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
              100: {
                
              },
              768: {

              },
              991: {

              },
              1250: {

              },
        },
      });

var sliders = new Swiper(".category_open", {
        slidesPerView: 4,
        spaceBetween: 20,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
              100: {
                slidesPerView: 2,
                spaceBetween: 16,
              },
              768: {
                slidesPerView: 2,
              },
              991: {
                slidesPerView: 3,
              },
              1250: {

              },
        }
});
var swiper = new Swiper(".mySwiper", {
        direction: "vertical",
        spaceBetween: 35,
        slidesPerView: 4,
        watchSlidesProgress: true,
      });
      var swiper2 = new Swiper(".mySwiper2", {
        effect: 'fade',
          fadeEffect: {
            crossFade: true
          },
        slidesPerView: 1,
        loop: true,
        spaceBetween: 100,
        thumbs: {
          swiper: swiper,
        },
      });

var swiper3 = new Swiper(".mySwiper3", {
        slidesPerView: 1,
      });


if (window.innerWidth < 768) {
var blog = new Swiper(".blog_open__gallery", {
        slidesPerView: 2,
        spaceBetween: 16,
        centeredSlides: true,
        loop: true,
        breakpoints: {
              100: {
                slidesPerView: 2,
                spaceBetween: 16,
              },
              768: {
                enabled: false,
              },
              991: {
                enabled: false,
              },
              1250: {
                enabled: false,
              },
        }
});
}

$(".burger").on("click", function() {
  $("body").toggleClass("active");
});

var ingr = new Swiper(".sets_item", {
        slidesPerView: 4,
        spaceBetween: 12,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        breakpoints: {
          100: {
            slidesPerView: 3,
          },
          420: {
            slidesPerView: 4,
          },
          600: {
            slidesPerView: 5,
          },
          991: {
            slidesPerView: 3,
          },
          1200: {
            slidesPerView: 4,
          },
          
        }
      });

var colors = ['#FCE8F7', '#E5FFBF', '#EDEFFC', '#CCFFFE', '#F7FEEC'];
var x = document.getElementsByClassName("category__open_item--info");
for (i = 0; i < x.length; i++) {
  var c = Math.floor(Math.random() * colors.length);
  while ( color == colors[c] ) {
    var c = Math.floor(Math.random() * colors.length);
  }
  var color = colors[c];
  $(x[i]).css('background-color', color);
}


// $('.catalog__wrapper ul.tabs__caption').on('click', 'li:not(.active)', function() {
//     $(this)
//       .addClass('active').siblings().removeClass('active')
//       .closest('.catalog__wrapper').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
//   });

// $('.set_open--img_wrapper').on('click', '.set_open--img:not(.active)', function() {
//     $(this)
//       .addClass('active').siblings().removeClass('active');
//   });

// $('.cart__main--form ul.tabs__caption').on('click', 'li:not(.active)', function() {
//     $(this)
//       .addClass('active').siblings().removeClass('active')
//       .closest('.cart__main--form').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
//   });

// $(".range .plus").click(function() {
//   var $price = $(this).closest('.input').find('.sum')
//   if ($price !== $price) {
//     $price.val(parseInt(1));
//   }
//   $price.val(parseInt($price.val()) + 1);
//   $price.change();
// });
// $(".range .minus").click(function() {
//   var $price = $(this).closest('.input').find('.sum')
//   $price.val(parseInt($price.val()) - 1);
//   $price.change();
// });

$('.custom_input').on('click', function() {
  var copyText = document.getElementById("myInput");

  copyText.select();

  document.execCommand("copy");
});

// function usePhoneMask($) {
//     if (exist('input.phone-mask, input.uk-phoneMask')) {
//         $('input.phone-mask, input.uk-phoneMask').inputmask('+38 (099) 99 99 999');
//     }
// }

function exist($target) {
    var $ = jQuery;
    return $($target).length ? true : false
}

let box = document.getElementById("basket-box");

box.onanimationend = function(event) {
  box.classList.remove("shake");
};

// usePhoneMask($);


});
