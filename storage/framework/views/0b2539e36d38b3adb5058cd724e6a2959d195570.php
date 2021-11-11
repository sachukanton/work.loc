<?php
    global $wrap;
?>


<?php $__env->startSection('content'); ?>
    <div class="container">
        
        <section class="cart">
            <a href="<?php echo e(url()->previous()); ?>"
               class="goback">
                <svg>
                    <use xlink:href="#left"></use>
                </svg>
                <h6><?php echo variable('back'); ?></h6>
            </a>
            <?php echo $__env->make('frontend.default.shops.gifts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="cart__main">
                <div class="cart__main--items">
                    <h4><?php echo app('translator')->getFromJson('shop.titles.your_order'); ?></h4>
                    <?php echo $_item->checkoutProductsOutput; ?>

                    

                </div>
                <div class="cart__main--form">
                    <h4><?php echo $wrap['page']['title']; ?></h4>
                    <?php echo $_item->checkoutFormOutput; ?>

                </div>
            </div>


        </section>
        <div class="recommend-order">
            <load-component
                entity="shop_product_view_list_recommended_checkout"
                options=""></load-component>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    

    <link href="/dashboard/css/air-datepicker.min.css"
          rel="stylesheet">
    
    
    
    
    
    
    
    
    <script src="/template/js/vue.js"
            type="text/javascript"></script>
    
    
    <script type="text/javascript">
        if (typeof fbq == 'function') {
            var a = {};
            if (typeof FbData == 'object') a = Object.assign(a, FbData);
            a.content_type = 'product';
            a.content_ids = <?php echo isset($_basket->sku_list) ? $_basket->sku_list : NULL; ?>;
            a.value = <?php echo e(isset($_basket->amount) ? $_basket->amount['original']['price'] : NULL); ?>;
            a.currency = 'UAH';
            a.num_items = <?php echo e(isset($_basket->quantity_in) ? $_basket->quantity_in : NULL); ?>;
            fbq('track', 'InitiateCheckout', a);
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.default.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/pages/checkout.blade.php ENDPATH**/ ?>