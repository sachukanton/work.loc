<?php
    $_level = $_level ?? 0;
    $_level ++;
 $_device_type = wrap()->get('device.type');
?>
<li <?php echo render_attributes($_item['item']['wrapper']); ?>>
    <?php echo $_item['item']['prefix']; ?>

    <?php if($_item['item']['active'] || is_null($_item['item']['path'])): ?>
        <span class="uk-navbar-toggle">
             <?php if($_item['item']['icon']): ?>
             <img data-src="<?php echo e($_item['item']['icon']); ?>" uk-img <?php if($_device_type != 'mobile'): ?> uk-svg <?php endif; ?> class="menu-icon uk-preserve uk-margin-small-right"
                  alt="<?php echo e($_item['item']['title']); ?>" height="30px" width="30px">
            <?php endif; ?>
            <?php echo $_item['item']['title']; ?>

        </span>
    <?php else: ?>
        <a <?php echo render_attributes($_item['item']['attributes']); ?>>
            <?php if($_item['item']['icon']): ?>
            <img data-src="<?php echo e($_item['item']['icon']); ?>" uk-img <?php if($_device_type != 'mobile'): ?> uk-svg <?php endif; ?> class="menu-icon uk-preserve uk-margin-small-right"
                 alt="<?php echo e($_item['item']['title']); ?>" height="30px" width="30px">
            <?php endif; ?>
            <?php echo e($_item['item']['title']); ?>

        </a>
    <?php endif; ?>
    <?php if($_item['children']->isNotEmpty()): ?>
        <ul class="sub-nav">
            <?php $__currentLoopData = $_item['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_sub_item_menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('frontend.default.menus.menu_item', ['_item' => $_sub_item_menu, '_level' => $_level], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
    <?php echo $_item['item']['suffix']; ?>

</li>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/menus/menu_item.blade.php ENDPATH**/ ?>