<div id="uk-items-list-top-bar"
     class="uk-clearfix uk-filter-sort">
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <div class="uk-position-relative sort-catalog box-dropdown">
        <button class="uk-button uk-button-default uk-flex uk-flex-between uk-flex-middle btn-dropdown"
                type="button">
            <?php echo e($_sort['use']['title']); ?>

            <img uk-img="data-src:<?php echo e(formalize_path('template/images/icon-arrow-down.svg')); ?>"
                 alt="">
        </button>
        <div uk-dropdown="mode: click; pos: bottom-left; boundary: .lang-box; animation: false; duration: 0;flip:false">
            <ul class="uk-nav uk-nav-default uk-position-relative">
                <?php $__currentLoopData = $_sort['list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e($_link['alias']); ?>"
                           class="use-ajax">
                            <?php echo e($_link['title']); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/base/shop_top_bar.blade.php ENDPATH**/ ?>