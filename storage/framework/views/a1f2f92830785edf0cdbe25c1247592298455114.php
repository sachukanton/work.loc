<div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
    <h2 class="uk-heading-line">
        <span>
            <?php echo app('translator')->getFromJson('shop.titles.last_complete_orders'); ?>
        </span>
    </h2>
    <?php echo $__env->make('backend.shop.partials.items_last_complete_orders', ['_items' => $_others['last_complete_orders']], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/shop/partials/box_last_complete_orders.blade.php ENDPATH**/ ?>