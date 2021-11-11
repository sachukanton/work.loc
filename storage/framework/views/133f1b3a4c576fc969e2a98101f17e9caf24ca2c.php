

<?php $__env->startSection('content'); ?>
<section class="not_found">
    <div class="container" style="background-image: url(template/images/404-side.png);">
        <div class="not_found__wrapper">
            <img src="template/images/404.png" alt="404">
            <p><?php echo variable('not_found'); ?></p>
        </div>
    </div>
</section>
<?php echo menu_render('2', ['view'=>'frontend.default.menus.menu_2_404']); ?>
   <!--  <div class="error-404 uk-flex uk-flex-bottom">
    <div class="uk-container uk-container-small ">
        <div class="box-error uk-text-center">
            
                
                    
                    
                
            
            <div class="title uk-margin-remove">
                <?php echo app('translator')->getFromJson('frontend.page_not_found'); ?>
            </div>
            <div class="box-btn-link uk-margin-bottom">
                <a href="<?php echo e(_u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/'))); ?>"
                   class="uk-button uk-button-link">
                    <?php echo app('translator')->getFromJson('frontend.go_home'); ?>
                </a>
            </div>
        </div>
    </div>
    </div> -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // window.setTimeout(function () {
    //     window.location.href = '/';
    // }, 5000);
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('errors.minimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/errors/404.blade.php ENDPATH**/ ?>