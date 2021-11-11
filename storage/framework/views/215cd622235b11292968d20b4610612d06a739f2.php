<?php if($_items->isNotEmpty()): ?>
    <section class="new__section">
        <div class="container">
            <?php if(isset($_title)): ?>
                <h2>
                    <?php echo $_title; ?>

                </h2>
            <?php endif; ?>
            <div class="category_open_checkout">
                <?php $__currentLoopData = $_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('frontend.default.shops.product_teaser_checkout', ['_item' => $_product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/load_entities/view_lists_recommended_checkout_product.blade.php ENDPATH**/ ?>