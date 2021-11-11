

<?php $__env->startSection('content'); ?>
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin uk-text-color-teal">
                <?php echo $_wrap['seo']['title']; ?>

            </h1>
        </div>
        <?php echo $__env->make('backend.shop.partials.box_new_orders', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('backend.shop.partials.box_last_complete_orders', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php if(isset($_others['journal'])): ?>
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                <div class="uk-card-body">
                    <h2 class="uk-heading-line"><span>Журнал событий</span></h2>
                    <ul class="uk-list">
                        <?php $__currentLoopData = $_others['journal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="uk-alert-<?php echo e($_message->class); ?> uk-padding-small uk-border-rounded">
                                <div uk-grid>
                                    <div class="uk-width-auto">
                                        <?php echo e($_message->created_at); ?>

                                    </div>
                                    <div class="uk-width-expand">
                                        <?php echo $_message->message; ?>

                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <div id="load-update-order-lists"></div>
        <script>
            window.update_order_lists = true;
            window.update_order_lists_last_create_at = null;
        </script>
    </article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/main/index.blade.php ENDPATH**/ ?>