<?php
   global $wrap;
   $_basket = app('basket');
   $locale = $wrap['locale'] ?? DEFAULT_LOCALE;
   $_device_type = $wrap['device']['type'] ?? 'pc';
?>

<div id="basket-box">
    <a href="<?php echo e($wrap['seo']['base_url'] . _r('page.shop_checkout')); ?>"
       rel="nofollow"
       class="bag <?php echo e($_basket->exists ? ' not-empty' : ' uk-disabled'); ?>">
        
        <svg>
            <use xlink:href="#bag"></use>
        </svg>
        <?php if($_basket->exists): ?>
            <span>
                <?php echo $_basket->quantity_in; ?>

            </span>
            <?php else: ?>
            <span>0</span>
        <?php endif; ?>
    </a>
</div>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/load_entities/store_management_block.blade.php ENDPATH**/ ?>