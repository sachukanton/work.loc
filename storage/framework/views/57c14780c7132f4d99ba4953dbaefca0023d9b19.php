<?php if($paginator->hasPages()): ?>
    <ul class="uk-pagination uk-flex-center uk-flex-middle uk-margin-top">
        <?php if($paginator->onFirstPage()): ?>
            <li class="uk-disabled">
                <span uk-pagination-previous></span>
            </li>
        <?php else: ?>
            <li>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>">
                    <span uk-pagination-previous></span>
                </a>
            </li>
        <?php endif; ?>
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_string($element)): ?>
                <li class="uk-disabled">
                    <span><?php echo e($element); ?></span>
                </li>
            <?php endif; ?>
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <li class="uk-active">
                            <span><?php echo e($page); ?></span>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo e($url); ?>">
                                <?php echo e($page); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($paginator->hasMorePages()): ?>
            <li>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>">
                    <span uk-pagination-next></span>
                </a>
            </li>
        <?php else: ?>
            <li class="uk-disabled">
                <span uk-pagination-next></span>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/partials/pagination.blade.php ENDPATH**/ ?>