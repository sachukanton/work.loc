<?php $__env->startSection('content'); ?>
    <div class="error-404 uk-flex uk-flex-bottom" style="background-image: url('template/images/bg-500.png')">
    <div class="uk-container uk-container-small">
        <div class="box-error error-server uk-text-center">
            
                
                    
                    
                
            
            <div class="title uk-margin-remove">
                <?php echo app('translator')->getFromJson('frontend.page_server_error'); ?>
            </div>
            <div class="box-btn-link uk-margin-bottom">
                <a href="<?php echo e(_u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/'))); ?>"
                   class="uk-button uk-button-link">
                    <?php echo app('translator')->getFromJson('frontend.go_home'); ?>
                </a>
            </div>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // window.setTimeout(function () {
    //     window.location.href = '/';
    // }, 5000);
</script>
<?php $__env->stopPush(); ?>




<?php echo $__env->make('errors.minimal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Web\work.loc\resources\views/errors/500.blade.php ENDPATH**/ ?>