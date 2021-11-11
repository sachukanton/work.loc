<?php if($_items->isNotEmpty()): ?>
    <section class="new__section">
        <div class="container">
                <?php if(isset($_title)): ?>
                    <h2>
                        <?php echo $_title; ?>

                    </h2>
                <?php endif; ?>
            <div class="swiper-container category_open">
                <div class="swiper-btn">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="swiper-wrapper">
                <?php $__currentLoopData = $_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('frontend.default.shops.product_teaser', ['_item' => $_product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/load_entities/view_lists_product.blade.php ENDPATH**/ ?>