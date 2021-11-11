

<?php $__env->startSection('content'); ?>
    <div class="uk-container">
        <article class="uk-article uk-position-relative page-user">
            <h1 class="title-01 title-default uk-position-relative uk-position-z-index">
                <?php echo $_wrap['page']['title']; ?>

            </h1>
            <?php if($_item->sub_title): ?>
                <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                    <?php echo $_item->sub_title; ?>

                </div>
            <?php endif; ?>
            <div class="uk-content-body uk-margin-medium-bottom">
                <div class="uk-width-1-2@m uk-width-2-3@s uk-margin-auto-left uk-margin-auto-right uk-text-center">
                    <?php echo $_item->loginFormOutput; ?>

                </div>
            </div>
        </article>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.default.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/user/login.blade.php ENDPATH**/ ?>