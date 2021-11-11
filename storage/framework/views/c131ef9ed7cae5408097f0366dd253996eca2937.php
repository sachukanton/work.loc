<?php
    global $wrap;
    $_device_type = wrap()->get('device.type');
?>
<div id="form-checkout-order-products">
    <?php if(isset($_items) && $_items): ?>
        <div class="cart__main--items-wrapper">
            <?php $__currentLoopData = $_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_key => $_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $_product->composition; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="cart__main--item">
                            
                                    <div class="cart__main_img">
                                        <?php if($_product->preview_fid): ?>
                                            
                                                
                                            
                                                <?php echo image_render($_product->_preview, NULL, ['attributes' => ['title' => $_product->title, 'alt' => $_product->title]]); ?>

                                            
                                        <?php else: ?>
                                            <?php echo image_render(NULL, 'productTeaser_188_125', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_product->title), 'width' => 140]]); ?>

                                        <?php endif; ?>
                                    </div>
                            
                            <div class="cart__main_name">
                                <?php echo _l(str_limit(strip_tags($_product->title), 50) . ($_comp['key'] === 'certificate' ? ' [СЕРТИФИКАТ]' : null), $_product->generate_url,
                                ['attributes' => ['title' => strip_tags(str_replace(["'",'"'], '',
                                $_product->title)), 'class' => '']]); ?>
                            </div>
                            <div class="cart__main_size">
                                <?php if(($_param = ($_product->paramOptions[3] ?? NULL))): ?>
                                    <div id="shop-product-weight-box">
                                        
                                        <?php echo e($_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL)); ?>

                                    </div>
                                <?php endif; ?>
                                <?php if(($_param = ($_product->paramOptions[4] ?? NULL))): ?>
                                    <div>
                                        <?php if($_product->paramOptions[3] ?? NULL): ?>&nbsp;-&nbsp;<?php endif; ?>
                                        <?php echo e($_param['options'] . ($_param['unit'] ? " {$_param['unit']}" : NULL)); ?>

                                    </div>
                                <?php endif; ?>
                                <?php if(($_param = ($_product->paramOptions[6] ?? NULL))): ?>
                                    <?php echo $_param['options'] . ' ' . $_param['unit']; ?>

                                <?php endif; ?>
                            </div>
                            <!-- <div class="cart__main_price-old">
                                <?php if($_product->price_certificate && $_comp['key'] === 'certificate'): ?>
                                    <span style="text-decoration: line-through;"
                                          class="uk-text-danger"><?php echo $_product->price['format']['view_price']; ?></span>
                                    <?php echo $_product->price_certificate['format']['view_price_2']; ?>

                                <?php else: ?>
                                    <?php echo $_product->price['format']['view_price_2']; ?>

                                <?php endif; ?>
                            </div> -->
                            <!-- <?php if(($_param = ($_product->paramOptions[2] ?? NULL))): ?>
                                <div class="consist-checkout">
                                    <?php
                                        $_param_values = NULL;
                                        foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                                    ?>
                                    <div class="param-values uk-overflow-hidden">
                                        <?php echo e(str_limit(implode(', ', $_param_values),150)); ?>

                                    </div>
                                </div>
                            <?php endif; ?> -->
                                <div class="input uk-input-number-counter-box">
                                    <input class="sum"
                                            type="number"
                                            value="<?php echo e($_comp['quantity']); ?>"
                                            min="1"
                                            data-default="1"
                                            data-callback="recountBasketProducts"
                                            data-e="<?php echo e("{$_product->price_id}::{$_comp['spicy']}::{$_comp['key']}"); ?>"
                                            step="1"
                                            name="count"
                                            <?php echo e($_comp['key'] === 'certificate' ? 'disabled' : NULL); ?>

                                            class="uk-input uk-text-center uk-disabled"
                                            autocapitalize="off">
                                    <div class="range">
                                        <button type="button"
                                                name="increment"
                                                class="plus"
                                                <?php echo e($_comp['key'] === 'certificate' ? 'disabled' : NULL); ?>>
                                            +
                                        </button>
                                        <button type="button"
                                                class="minus"
                                                <?php echo e($_comp['quantity'] == 1 ? 'disabled' : NULL); ?>

                                                name="decrement">
                                            -
                                        </button>
                                    </div>
                                </div>
                                <div class="cart__main_price">
                                    <?php if($_product->price_certificate && $_comp['key'] === 'certificate'): ?>
                                        <span style="text-decoration: line-through;"
                                              class="uk-text-danger"><?php echo $_product->price['format']['view_price']; ?></span>
                                        <?php echo $_product->price_certificate['format']['view_price_2']; ?>

                                    <?php else: ?>
                                        <?php echo $_product->price['format']['view_price_2']; ?>

                                    <?php endif; ?>
                                </div>
                                <div class="cart__main_price-old">
                                    <?php echo $_product->_price->old_price; ?>

                                </div>
                                <a href="<?php echo e(_r('ajax.checkout_remove_products')); ?>"
                                   rel="nofollow"
                                   data-e="<?php echo e("{$_product->price_id}::{$_comp['spicy']}::{$_comp['key']}"); ?>"
                                   class="trash">
                                    <svg>
                                        <use xlink:href="#trash"></use>
                                    </svg>
                                </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="checkout-warning-text">
            <?php echo variable('warning_text_on_checkout_page'); ?>
        </div>
    <?php else: ?>
        <div class="uk-alert uk-alert-warning">
            <?php echo app('translator')->getFromJson('frontend.basket_is_empty'); ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/checkout_products.blade.php ENDPATH**/ ?>