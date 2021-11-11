<?php
    $_level = $_level ?? 0;
    $_level ++;
//  if($_level == 1) {
//        $_item['item']['attributes']['class'][] = 'uk-text-bold uk-text-uppercase';
//    }
    $_item['item']['wrapper']['class'][] = "uk-flex";
    $_item['item']['attributes']['class'][] = "category__item";
    $_device_type = wrap()->get('device.type');
?>
    <?php if($_item['item']['active'] || is_null($_item['item']['path'])): ?>
        <div class="category__item">
            <h3>
                <?php echo $_item['item']['title']; ?>

            </h3>
            <div class="category__item--img">
                <img data-src="<?php echo e($_item['item']['icon']); ?>" uk-img uk-svg class="menu-icon uk-preserve"
                     alt="<?php echo e($_item['item']['title']); ?>">
            </div>
        </div>
    <?php else: ?>
        <a <?php echo render_attributes($_item['item']['attributes']); ?>>
            <h3>
                <?php echo $_item['item']['title']; ?>

            </h3>
            <div class="category__item--img">
                <img data-src="<?php echo e($_item['item']['icon']); ?>" uk-img uk-svg class="menu-icon uk-preserve"
                     alt="<?php echo e($_item['item']['title']); ?>">
            </div>
        </a>
    <?php endif; ?>
    <?php echo $_item['item']['suffix']; ?>

</li>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/menus/menu_2_item.blade.php ENDPATH**/ ?>