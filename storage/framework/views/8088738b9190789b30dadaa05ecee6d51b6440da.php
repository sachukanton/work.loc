<!DOCTYPE html>
<html lang="<?php echo e($_wrap['locale']); ?>">
    <head>
        <base href="<?php echo e($_wrap['seo']['base_url']); ?>">
        <title><?php echo e(strip_tags($_wrap['seo']['title'])); ?></title>
        <meta name="robots"
              content="noindex, nofollow" />
        <meta charset="utf-8">
        <meta http-equiv="Content-Type"
              content="text/html; charset=utf-8" />
        <meta name="viewport"
              content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="csrf-token"
              content="<?php echo e($_wrap['token']); ?>">
        <?php if(isset($_wrap['page']['favicon']) && $_wrap['page']['favicon']): ?>
            <link href="/favicon.ico"
                  rel="shortcut icon"
                  type="image/x-icon" />
        <?php endif; ?>
        <script type="text/javascript">
            window.Laravel = <?php echo isset($_wrap['page']['js_settings']) ? (is_array($_wrap['page']['js_settings']) ? json_encode($_wrap['page']['js_settings']) : $_wrap['page']['js_settings']) : json_encode([]); ?>;
        </script>
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
        <?php if(isset($_wrap['page']['scripts']['header']) && ($_link_scripts_in_head = $_wrap['page']['scripts']['header'])): ?>
            <?php echo $_link_scripts_in_head; ?>

        <?php endif; ?>
    </head>
    <body <?php echo $_wrap['page']['attributes'] ?? NULL; ?>>
        <div class="uk-margin-bottom">
            <div class="uk-top-bar uk-background-color-white">
                <div class="uk-navbar-container uk-background-color-transparent uk-padding-small-horizontal"
                     uk-navbar>
                    <div class="uk-navbar-left">
                        <?php echo $__env->make('backend.menus.admin_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="uk-navbar-right">
                        <div>
                            <button class="uk-button uk-button-default"
                                    type="button">
                                <?php echo e($_wrap['user']->full_name); ?>

                                <span uk-icon="keyboard_arrow_down"
                                      class="uk-margin-small-left"></span>
                            </button>
                            <div uk-dropdown="mode: click; pos: bottom-right;">
                                <ul class="uk-nav uk-dropdown-nav">
                                    <li>
                                        <?php echo _l('<span uk-icon="power_settings_new"></span> Выйти', 'logout', ['attributes' => ['class' => 'uk-text-danger']]); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if($_authUser->hasRole('super_admin')): ?>
                            <div>
                                <button class="uk-button uk-button-warning uk-margin-small-left"
                                        type="button">
                                    <span uk-icon="cached"></span>
                                </button>
                                <div uk-dropdown="mode: click; pos: bottom-right;">
                                    <ul class="uk-nav uk-dropdown-nav">
                                        <li>
                                            <?php echo _l('Clear CACHE', 'oleus.artisan', ['p' => ['command' => 'clear', 'target' => 'cache'], 'attributes' => ['class' => 'uk-text-danger']]); ?>
                                        </li>
                                        <li>
                                            <?php echo _l('Clear VIEW', 'oleus.artisan', ['p' => ['command' => 'clear', 'target' => 'view'], 'attributes' => ['class' => 'uk-text-danger']]); ?>
                                        </li>
                                        <li>
                                            <?php echo _l('Clear ROUTE', 'oleus.artisan', ['p' => ['command' => 'clear', 'target' => 'route'], 'attributes' => ['class' => 'uk-text-danger']]); ?>
                                        </li>
                                        <li>
                                            <?php echo _l('Clear CONFIG', 'oleus.artisan', ['p' => ['command' => 'clear', 'target' => 'config'], 'attributes' => ['class' => 'uk-text-danger']]); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo e(_u('/')); ?>"
                           target="_blank"
                           class="uk-button uk-button-success uk-margin-small-left">
                            <span uk-icon="launchopen_in_new"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-container-large uk-margin-auto-left uk-margin-auto-right uk-margin-top">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <?php if(isset($_wrap['page']['styles']['footer']) && ($_link_styles_in_footer = $_wrap['page']['styles']['footer'])): ?>
            <?php echo $_link_styles_in_footer; ?>

        <?php endif; ?>
        <?php if(isset($_wrap['page']['scripts']['footer']) && ($_link_scripts_in_footer = $_wrap['page']['scripts']['footer'])): ?>
            <?php echo $_link_scripts_in_footer; ?>

        <?php endif; ?>
        <?php echo $__env->yieldPushContent('styles'); ?>
        <?php echo $__env->yieldPushContent('scripts'); ?>
        <?php echo $__env->make('backend.partials.notice', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </body>
</html>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/index.blade.php ENDPATH**/ ?>