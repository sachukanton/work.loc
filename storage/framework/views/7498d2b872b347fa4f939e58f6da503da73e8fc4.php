<?php
    $_gifts = isset($gifts) ? $gifts : \App\Models\Shop\Gift::getInfo();
?>
<?php if($_gifts): ?>
    <div id="gifts-box"
         class="cart__top">
        <div class="wrapper">
            <?php $__currentLoopData = $_gifts['steps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="wrapper_inner<?php echo e($_step['checked'] ? ' active' : NULL); ?>">
                    <div class="wrapperes">
                        <div class="cart__top--item"
                             style="background-image: url('<?php echo e($_step['image_url']); ?>');">
                            <svg>
                                <use xlink:href="#check"></use>
                            </svg>
                        </div>
                        <h6 class="<?php echo e($_step['checked'] ? 'active' : NULL); ?>">
                            <?php echo e($_step['title']); ?>

                        </h6>
                    </div>
                </div>
                <span class="arrows<?php echo e($_step['checked'] ? ' active' : NULL); ?>">
                    <svg>
                        <use xlink:href="#arrows_dwn"></use>
                    </svg>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        
        <?php if($_gifts['view_text']): ?>
            <div class="go_more">
                <h6><?php echo $_gifts['view_text']; ?></h6>
            </div>
        <?php endif; ?>
        <div class="minmax_time">
            <div class="minmax">
                <span class="minmax_img">
                    <img src="<?php echo e(formalize_path('template/images/icons/clock.svg')); ?>"
                         alt="<?php echo variable('max_time_1'); ?>">
                </span>
                <div><?php echo variable('max_time_1'); ?></div>
            </div>
            <div class="minmax">
                <span class="minmax_img">
                    <img src="<?php echo e(formalize_path('template/images/icons/clock.svg')); ?>"
                         alt="<?php echo variable('max_time_2'); ?>">
                </span>
                <div><?php echo variable('max_time_2'); ?></div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/gifts.blade.php ENDPATH**/ ?>