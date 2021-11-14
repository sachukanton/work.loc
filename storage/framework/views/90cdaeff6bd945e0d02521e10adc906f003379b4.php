<?php if(isset($_wrap) && is_array($_wrap)): ?>
<?php
    $_analytics_google = $_wrap['services']['googleTag'] ?? NULL;
    $_analytics_facebook = $_wrap['services']['facebookPixel'] ?? NULL;
    $_reCaptcha = $_wrap['services']['reCaptcha'] ?? NULL;
    $_top_logotype = $_wrap['page']['logotype']['top'];
    $_footer_logotype = $_wrap['page']['logotype']['footer'] ? $_wrap['page']['logotype']['footer'] : $_top_logotype;
    $_top_mobile = $_wrap['page']['logotype']['mobile'] ? $_wrap['page']['logotype']['mobile'] : $_top_logotype;
    $_device_type = $_wrap['device']['type'];
?>
        <!DOCTYPE html>
<html lang="<?php echo e($_wrap['locale']); ?>"
      class="uk-background-color-white uk-height-min-vh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <base href="<?php echo e($_wrap['seo']['base_url']); ?>">
    <title><?php echo e(strip_tags("{$_wrap['seo']['title']} {$_wrap['seo']['title_suffix']}{$_wrap['seo']['page_number_suffix']}")); ?></title>
    <meta name="description"
          content="<?php echo e($_wrap['seo']['description'] . $_wrap['seo']['page_number_suffix']); ?>">
    <meta name="keywords"
          content="<?php echo e($_wrap['seo']['keywords']); ?>">
    <meta name="robots"
          content="noindex, nofollow"/>
    <?php if(isset($_wrap['seo']['last_modified']) && $_wrap['seo']['last_modified']): ?>
        <meta http-equiv="Last-Modified"
              content="<?php echo e($_wrap['seo']['last_modified']); ?>">
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['url']) && $_wrap['seo']['url']): ?>
        <meta name="url"
              content="<?php echo e($_wrap['seo']['base_url'] . $_wrap['seo']['url']); ?>">
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['canonical']) && $_wrap['seo']['canonical']): ?>
        <link rel="canonical"
              href="<?php echo e($_wrap['seo']['base_url'] . $_wrap['seo']['canonical']); ?>"/>
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['color']) && $_wrap['seo']['color']): ?>
        <meta name="theme-color"
              content="<?php echo e($_wrap['seo']['color']); ?>">
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['copyright']) && $_wrap['seo']['copyright']): ?>
        <meta name="copyright"
              content="<?php echo e($_wrap['seo']['copyright']); ?>">
    <?php endif; ?>
    <meta name="csrf-token"
          content="<?php echo e(csrf_token()); ?>">
    <?php if(isset($_wrap['page']['favicon']) && $_wrap['page']['favicon']): ?>
        <link href="/favicon.ico"
              rel="shortcut icon"
              type="image/x-icon"/>
        
        
        
        
        
        
        
        
        
        
        
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['link_prev']) && $_wrap['seo']['link_prev']): ?>
        <link rel="prev"
              href="<?php echo e($_wrap['seo']['base_url'] . $_wrap['seo']['link_prev']); ?>"/>
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['link_next']) && $_wrap['seo']['link_next']): ?>
        <link rel="next"
              href="<?php echo e($_wrap['seo']['base_url'] . $_wrap['seo']['link_next']); ?>"/>
    <?php endif; ?>
    <link rel="preconnect"
          href="<?php echo e($_wrap['seo']['base_url']); ?>">
    <link rel="dns-prefetch"
          href="<?php echo e($_wrap['seo']['base_url']); ?>">
    <link rel="preconnect"
          href="//fonts.googleapis.com">
    <link rel="dns-prefetch"
          href="//fonts.googleapis.com">
    <link rel="preconnect"
          href="//fonts.gstatic.com/"
          crossorigin>
    <link rel="dns-prefetch"
          href="//fonts.gstatic.com/">
    <?php if(isset($_wrap['page']['styles']['header']) && ($_link_styles_in_head = $_wrap['page']['styles']['header'])): ?>
        <?php echo $_link_styles_in_head; ?>

    <?php endif; ?>
    <script type="text/javascript">
        window.Laravel = <?php echo isset($_wrap['page']['js_settings']) ? (is_array($_wrap['page']['js_settings']) ? json_encode($_wrap['page']['js_settings']) : $_wrap['page']['js_settings']) : json_encode([]); ?>;
        var FbData = {
            locale: '<?php echo e($_wrap['locale']); ?>',
            device: '<?php echo e($_wrap['device']['type']); ?>',
            path: '<?php echo e($_wrap['seo']['url']); ?>',
            content_name: '<?php echo e($_wrap['page']['title']); ?>',
        };
        window.reCaptchaKey = '<?php echo e($_reCaptcha); ?>';
    </script>
    <?php if(isset($_wrap['page']['scripts']['header']) && ($_link_scripts_in_head = $_wrap['page']['scripts']['header'])): ?>
        <?php echo $_link_scripts_in_head; ?>

    <?php endif; ?>
    <?php if($_analytics_google): ?>
        <script async
                src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($_analytics_google); ?>"></script>
        <script type="text/javascript">
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '<?php echo e($_analytics_google); ?>');
            gtag('config', 'AW-467931757');
        </script>
    <?php endif; ?>
    <?php if($_analytics_facebook): ?>
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?php echo e($_analytics_facebook); ?>');
            fbq('track', 'PageView', FbData);
        </script>
        <noscript>
            <img height="1"
                 width="1"
                 style="display:none"
                 src="https://www.facebook.com/tr?id=<?php echo e($_analytics_facebook); ?>&ev=PageView&noscript=1"
            />
        </noscript>
    <?php endif; ?>
    <?php if(isset($_wrap['seo']['open_graph']) && $_wrap['seo']['open_graph'] && is_string($_wrap['seo']['open_graph'])): ?>
        <?php echo $_wrap['seo']['open_graph']; ?>

    <?php endif; ?>
</head>
<body <?php echo $_wrap['page']['attributes'] ?? NULL; ?>>
<div id="app" <?php if($_wrap['page']['is_front']): ?> <?php else: ?> class="not-front" <?php endif; ?>>
    <header>
        <div class="container">
            <div class="header__wrapper">
                <div class="burger_wrapper">
                    <div class="burger" onclick="this.classList.toggle('active')">
                        <span class="burger_item1"></span>
                        <span class="burger_item2"></span>
                    </div>
                </div>
                <div class="mobile__nav">
                    @menuRender('1')
                    <div class="mobile__nav-bottom">
                        <div class="work_time">
                            <p><?php echo e(variable('work_today_title')); ?></p>
                            <span>с <b>10:30</b> до <b>22:00</b></span>
                        </div>
                        <a href="tel+38 (063) 629 69 04">+38 (063) 629 69 04</a>
                    </div>
                </div>
                <div class="header__left">
                    <div class="header__left--info">
                        <div class="header__left--icon">
                            <img src="template/images/icons/call.svg" alt="">
                        </div>
                        <div class="header__left--city">
                            <?php echo e(variable('city')); ?>

                        </div>
                        <?php if($_wrap['loads']['contacts']['phones'][0]): ?>
                        <?php echo $_wrap['loads']['contacts']['phones'][0]['format_render_3']; ?>

                        <?php endif; ?>
                        <?php if($_wrap['loads']['contacts']['working_hours']): ?>
                            <?php if(variable('work_today_title')): ?>
                                <div class="header__left--day">
                                    <?php echo e(variable('work_today_title')); ?>

                                </div>
                            <?php endif; ?>
                            <div class="header__left--time">
                                <?php echo $_wrap['loads']['contacts']['working_hours']; ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="btn--white">
                        <button type="button"
                                data-path="/ajax/open-form/2"
                                data-index=""
                                data-view=""
                                data-path-title=""
                                id="call-back"
                                class="use-ajax">
                                <svg>
                                    <use xlink:href="#call"></use>
                                </svg>
                                <?php echo e(variable('callback')); ?>

                                
                        </button>
                    </div>
                </div>
                <div class="header__center">
                    <?php if($_wrap['page']['is_front']): ?>
                        <div class="header__logo">
                            @imageRender($_top_logotype, NULL)
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/')); ?>"
                           class="header__logo">
                            @imageRender($_top_logotype, NULL)
                        </a>
                    <?php endif; ?>
                </div>
                <div class="header__right">
                    <div class="wrapper">
                        <div class="header__right--social">
                            <?php $__currentLoopData = $_wrap['loads']['contacts']['socials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_key => $_social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($_social): ?>
                                    <a href="<?php echo e($_social); ?>"
                                       class=" social__item <?php echo e($_key); ?>"
                                       target="_blank">
                                        <svg>
                                            <use xlink:href="#<?php echo e($_key); ?>"></use>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($_wrap['use']['multi_language']): ?>
                        <ul class="lang-switcher">
                        <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_locale_code => $_properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="">
                        <?php if(LaravelLocalization::getCurrentLocale() == $_locale_code): ?>
                        <span class="current"><?php echo e($_properties['name']); ?></span>
                        <?php else: ?>
                        <a rel="alternate"
                        class=""
                        hreflang="<?php echo e($_locale_code); ?>"
                        href="<?php echo e(LaravelLocalization::getLocalizedURL($_locale_code, $_wrap['seo']['url'], [])); ?>">
                        <?php echo e($_properties['name']); ?>

                        </a>
                        <?php endif; ?>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <?php echo $__env->make('frontend.default.load_entities.store_management_block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </header>

    <main>
        <?php echo $__env->make('frontend.default.partials.breadcumb', ['_items' => $_wrap['page']['breadcrumb']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <?php echo $__env->yieldContent('content'); ?>
    </main>


        <footer>
        <div class="container">
            <div class="footer__left--info">
                <div class="footer__left--icon">
                     <img src="template/images/icons/call.svg" alt="call">
                </div>
                <div class="wrapper">
                    <div class="footer__left--city">
                        <?php echo variable('city'); ?>

                    </div>
                    <div class="footer__left--time">
                        <?php echo variable('work_today_title'); ?> <?php if($_wrap['loads']['contacts']['working_hours']): ?>
                           <?php echo $_wrap['loads']['contacts']['working_hours']; ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="footer__left--tel">
                <p><?php echo variable('checkout_title'); ?></p>
                <?php if($_wrap['loads']['contacts']['phones']): ?>
                <ul>
                    <?php $__currentLoopData = $_wrap['loads']['contacts']['phones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       <li>
                            <?php echo $phone['format_render_3']; ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php endif; ?>
            </div>
            <div class="footer_btn">
                <button type="button"
                    data-path="/ajax/open-form/2"
                    data-index=""
                    data-view=""
                    data-path-title=""
                    id="call-back"
                    class="use-ajax btn--white">
                    <?php echo variable('callback'); ?>

                </button>
            </div>
            <?php if($_wrap['page']['site_copyright']): ?>
            <div class="footer__left--copyright">
                <?php echo e($_wrap['page']['site_copyright']); ?>

            </div>
            <?php endif; ?>
            <div class="footer__center_logo">
                <div class="footer__center">
                    <a href="<?php echo e(LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/')); ?>" class="footer__logo">
                         @imageRender($_top_logotype, NULL)
                    </a>
                </div>
            </div>
            <div class="footer__right--nav">
                @menuRender('1')        
            </div>
            <div class="footer__right--social">
                <p><?php echo variable('social_network'); ?></p>
                <?php $__currentLoopData = $_wrap['loads']['contacts']['socials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_key => $_social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($_social): ?>
                        <a href="<?php echo e($_social); ?>"
                           class=" social__item <?php echo e($_key); ?>"
                           target="_blank">
                            <svg>
                                <use xlink:href="#<?php echo e($_key); ?>"></use>
                            </svg>
                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="footer__right--privacy">
                <ul>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <li><a href="#">Договор публичной оферты</a></li>
                </ul>
            </div>
            <a href="https://oleus.com.ua/"  target="_blank" class="footer__right--oleus">
                <img src="template/images/oleus.svg">
            </a>
        </div>
    </footer>
</div>

<?php if($_device_type != 'pc'): ?>
    <div class="uk-fix-menu uk-position-bottom-left uk-position-fixed">
        @menuRender('2')
    </div>
    <div id="offcanvas-menu"
         uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
            <div class="uk-flex uk-flex-between uk-flex-top">
                <button class="uk-offcanvas-close uk-position-relative"
                        type="button">
                    <img uk-img="data-src:<?php echo e(formalize_path('template/images/icon-close.svg')); ?>"
                         alt="" width="15" height="15">
                </button>
                <div>
                    <img uk-img="data-src:<?php echo e(formalize_path('template/images/sushi-man.png')); ?>"
                         alt="" width="63" height="58">
                </div>
            </div>

            @menuRender('1')

            <div class="uk-grid-collapse uk-grid uk-margin-small-bottom">
                <?php if($_wrap['loads']['contacts']['working_hours']): ?>
                    <div class="working-hours uk-width-2-3">
                        <?php if(variable('work_today_title')): ?>
                            <div class="title-04">
                                <?php echo e(variable('work_today_title')); ?>:
                            </div>
                        <?php endif; ?>
                        <div>
                            <?php echo $_wrap['loads']['contacts']['working_hours']; ?>

                        </div>
                    </div>
                <?php endif; ?>

                <?php if($_wrap['loads']['contacts']['socials']): ?>
                    <div class="cosial-link uk-width-1-3">
                        <?php $__currentLoopData = $_wrap['loads']['contacts']['socials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_key => $_social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($_social): ?>
                                <a href="<?php echo e($_social); ?>"
                                   class=" icon-<?php echo e($_key); ?>"
                                   target="_blank">
                                    <img class="uk-margin-remove" uk-img="data-src:<?php echo e(formalize_path('template/images/icon-' . $_key . '.svg')); ?>"
                                         alt="" width="22" height="22">
                                </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="uk-grid-collapse uk-grid">
                <div class="uk-width-2-3">
                    <?php if($_wrap['loads']['contacts']['phones'] || $_wrap['loads']['contacts']['address']): ?>
                        <?php if(variable('checkout_title')): ?>
                            <div class="title-04">
                                <?php echo e(variable('checkout_title')); ?>:
                            </div>
                        <?php endif; ?>
                        <?php if($_wrap['loads']['contacts']['phones']): ?>
                            <div class="phone">
                                <?php $__currentLoopData = $_wrap['loads']['contacts']['phones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $_phone['format_render_3']; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($_wrap['loads']['contacts']['address']): ?>
                            <div class="uk-address">
                                <?php echo $_wrap['loads']['contacts']['address']; ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="uk-width-1-3">
                    <img uk-img="data-src:<?php echo e(formalize_path('template/images/cards.png')); ?>"
                         alt="" width="172" height="53">
                </div>

            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($_wrap['page']['styles']['footer']) && ($_link_styles_in_footer = $_wrap['page']['styles']['footer'])): ?>
            <?php echo $_link_styles_in_footer; ?>

        <?php endif; ?>
        <?php if(isset($_wrap['page']['scripts']['footer']) && ($_link_scripts_in_footer = $_wrap['page']['scripts']['footer'])): ?>
            <?php echo $_link_scripts_in_footer; ?>

        <?php endif; ?>
        <?php echo $__env->yieldPushContent('styles'); ?>
        <?php echo $__env->yieldPushContent('scripts'); ?>
        
        
        
        
        
        <?php if(session('commands')): ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    setTimeout(function () {
                        var $commands = <?= session('commands') ?>;
                        for (var $i = 0; $i < $commands.length; ++$i) {
                            var command = $commands[$i];
                            if (window['cmd_' + command.command] != undefined) window['cmd_' + command.command](command.options);
                        }
                    }, 500);
                });
            </script>
        <?php endif; ?>
        <?php echo $__env->yieldPushContent('schema'); ?>
        <?php if($_wrap['loads']['contacts']['schema']): ?>
            <script type='application/ld+json'>
                <?php echo $_wrap['loads']['contacts']['schema']; ?>

            </script>
<?php endif; ?>

























<?php echo $__env->make('frontend.default.partials.modal_banner', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php else: ?>
    <?php
        abort(500)
    ?>
<?php endif; ?>
<?php /**PATH D:\Web\work.loc\resources\views/errors/minimal.blade.php ENDPATH**/ ?>