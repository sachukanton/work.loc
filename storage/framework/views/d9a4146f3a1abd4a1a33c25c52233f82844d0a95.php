<?php
  global $wrap;
?>

<?php if(isset($_items)): ?>
    <div class="last-nodes" data-src="<?php echo e(formalize_path('template/images/bg-top.jpg')); ?>" uk-img>
        <div class="uk-container">
            <h2 class="title-02 uk-position-relative uk-position-z-index">
                Наш блог
            </h2>
            <div class="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-3 uk-grid-small uk-grid"
                 uk-height-match="target: .title">
                <?php $__currentLoopData = $_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_node): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <?php echo $__env->make('frontend.default.nodes.node_teaser', ['_item' => $_node, '_class' => 'uk-height-1-1'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if(count($_items) >= 3): ?>
                <div class="uk-text-center uk-margin-top">
                    <?php if($wrap['routes']['blog']): ?>
                        <a href="<?php echo e($wrap['routes']['blog']); ?>"
                           class="uk-link-more uk-position-relative uk-position-z-index">
                            <?php echo app('translator')->getFromJson('frontend.link_more'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/load_entities/page_last_nodes.blade.php ENDPATH**/ ?>