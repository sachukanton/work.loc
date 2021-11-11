<?php
    global $wrap;
    $_device_type = $wrap['device']['type'] ?? 'pc';
?>


<?php $__env->startSection('content'); ?>
<section class="set_open">
    <div class="container">
        <a href="<?php echo e(url()->previous()); ?>" class="goback">
            <svg>
                <use xlink:href="#left"></use>
            </svg>
            <h6><?php echo e(variable('back')); ?></h6>
        </a>
        <div class="wrapper">
                <div class="set_open--img_wrapper">
                    <?php if($_mark_param = $_item->_param_items): ?>
                        <div class="marks">
                            <?php $__currentLoopData = $_mark_param; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_param_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($_param_item->param_id == 1): ?>
                                    <?php
                                        $_icon_mark = $_param_item->icon_fid ? f_get($_param_item->icon_fid) : NULL;
                                    ?>
                                        <?php echo image_render($_icon_mark); ?>

                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                    <?php if($_item->slideShow && count($_item->slideShow['slide']) > 1): ?>
                    <?php if($_device_type == 'pc'): ?>
                        <div class="swiper mySwiper2">
                    <?php else: ?>
                        <div class="swiper mySwiper3">
                    <?php endif; ?>
                        <div class="swiper-wrapper">
                            <?php $__currentLoopData = $_item->slideShow['slide']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="swiper-slide">
                                <?php echo $_slide; ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php if($_device_type == 'pc'): ?>
                    <div thumbsSlider="" class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <?php $__currentLoopData = $_item->slideShow['slide']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="swiper-slide">
                                <?php echo $_slide; ?>

                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                        <?php echo image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]); ?>

                    <?php endif; ?>

<!--                     <?php if($_item->slideShow && count($_item->slideShow['slide']) > 1): ?>
                        <div class="set_open--img active">
                            <?php echo image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]); ?>

                        </div>
                        <?php $__currentLoopData = $_item->slideShow['slide']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="set_open--img">
                                <?php echo $_slide; ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php echo image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]); ?>

                    <?php endif; ?> -->
                </div>
                <div class="set_open--info_wrapper">
                    <h4>
                        <?php echo $_wrap['page']['title']; ?>

                    </h4>
                    <?php if($_item->sub_title): ?>
                        <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                            <?php echo $_item->sub_title; ?>

                        </div>
                    <?php endif; ?>
                    
                        
                            
                                
                            
                        
                    
                    <div class="wrapper" id="shop-product-action-box">
                        <div class="set_open--info-size">
                            <?php if(($_param = ($_item->paramOptions[3] ?? NULL))): ?>
                                <div id="shop-product-weight-box">
                                    
                                    <?php echo e($_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL)); ?>

                                </div>
                            <?php endif; ?>
                            <?php if(($_param = ($_item->paramOptions[4] ?? NULL))): ?>
                                <div>
                                    <?php if($_item->paramOptions[3] ?? NULL): ?>&nbsp;/&nbsp;<?php endif; ?>
                                    <?php echo e($_param['options'] . ($_param['unit'] ? " {$_param['unit']}" : NULL)); ?>

                                </div>
                            <?php endif; ?>
                            <?php if(($_param = ($_item->paramOptions[7] ?? NULL))): ?>
                               <div id="shop-product-weight-box">
                                    <?php echo e($_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL)); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="set_open--info-comp">
                            <?php echo e(variable('composition')); ?>

                            <?php if(($_ingredients_param = ($_item->paramOptions[2] ?? NULL))): ?>
                                <div class="param-option-product">
                                    <div class="uk-text-lowercase">
                                        <?php echo e(implode(' | ', $_ingredients_param['options'])); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="price">
                            <?php if($_item->price['view_price']): ?>
                                <?php if(count($_item->price['view']) > 1): ?>
                                    <span class="old_price">
                                        <?php echo $_item->price['view'][0]['format']['view_price']; ?>

                                    </span>
                                    <span class="real-old price-format">
                                        <?php echo $_item->price['view'][1]['format']['view_price_2']; ?>

                                    </span>
                                <?php else: ?>
                                    <span class="real-old price-format uk-margin-remove">
                                        <?php echo $_item->price['view'][0]['format']['view_price_2']; ?>

                                    </span>
                                <?php endif; ?>
                                <div class="product-additional-default"></div>
                            <?php endif; ?>  
                        </div>
                            <?php echo $__env->make('frontend.default.shops.product_consist', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            
                        <div class="input uk-input-number-counter-box">
                            <input class="sum" type="number"
                                    value="1"
                                    data-default="1"
                                    min="1"
                                    max="10000000"
                                    step="1"
                                    name="count"
                                    class="uk-input uk-text-center uk-disabled"

                                    autocapitalize="off">
                            <div class="range">
                                <button type="button"
                                        name="increment"
                                        class="plus">
                                    +
                                </button>
                                <button type="button"
                                        class="minus"
                                        name="decrement"
                                        disabled>
                                    -
                                </button>
                            </div>
                        </div>
                        <?php if(($_param = ($_item->paramOptions[8] ?? NULL))): ?>
                        <div class="set-kit">
                            <div class="set-kit_top">
                                <p><?php echo e(variable('set')); ?></p>
                                <div class="set-kit--wrapper">
                                    <?php
                                        $i = 1;
                                        while ($i <= $_param['options']):
                                        ?>
                                        <span>
                                            <svg>
                                                <use xlink:href="#user"></use>
                                            </svg>
                                        </span>
                                    <?php
                                        $i++;
                                        endwhile;
                                    ?>
                                </div>
                            </div>
                            <p><?php echo e($_param['unit'] ? "{$_param['unit']}" : NULL); ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="delivery_wrapper">
                            <div class="delivery">
                                <img src="template/images/icons/clock.svg">
                                <?php echo variable('max_time_3'); ?>

                            </div>
                            <div class="btn__wrapper">
                                <button type="button"
                                        data-path="<?php echo e(_r('ajax.shop_buy_one_click')); ?>"
                                        data-product="<?php echo e($_item->id); ?>"
                                        class="btn--white use-ajax">
                                    <?php echo e(variable('one_click')); ?>

                                </button>
                                <button id="shop-product-buy-button" type="button"
                                    data-path="<?php echo e(_r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']])); ?>"
                                    class="btn">
                                    <svg>
                                        <use xlink:href="#bike"></use>
                                    </svg>
                                    <?php echo e(variable('cart')); ?>

                                </button>
                            </div>
                        </div>
                        
                            
                                
                                        
                                        
                                        
                                        
                                    
                                
                            
                        


                        <?php if($_item->modification_items && $_item->modification_items->count() > 1): ?>
                            <div class="uk-modification">
                                <?php $__currentLoopData = $_item->modification_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($_mod->generate_url); ?>"
                                       title="<?php echo e($_mod->title); ?>"
                                       class="uk-button uk-btn-mod <?php echo e($_mod->id == $_item->id ? 'uk-active uk-disabled' : NULL); ?>">
                                        <?php echo e($_mod->modify_param_item_title); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>



                        
                            
                                
                                    
                                        
                                    
                                    
                                        
                                            
                                                
                                                    
                                                           
                                                           
                                                           
                                                    
                                                
                                            
                                        
                                    
                                
                            
                        

                        
                            
                                
                                    
                                        
                                            
                                        
                                        
                                            
                                                
                                                    
                                                        
                                                               
                                                               
                                                               
                                                        
                                                    
                                                
                                            
                                        
                                    
                                
                                
                                    
                                        
                                            
                                        
                                        
                                            
                                                
                                                     
                                                    
                                                        
                                                            
                                                                   
                                                                   
                                                                   
                                                                   
                                                            
                                                        
                                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                    
                                                
                                            
                                        
                                    
                                
                                
                                    
                                        
                                            
                                        
                                        
                                            
                                                
                                                     
                                                    
                                                        
                                                            
                                                                   
                                                                   
                                                                   
                                                            
                                                        
                                                    
                                                    
                                                         
                                                        
                                                    
                                                    
                                                         
                                                        
                                                                
                                                                
                                                                
                                                        
                                                        
                                                            
                                                        
                                                        
                                                                
                                                                
                                                        
                                                    
                                                
                                            
                                        
                                    
                                
                                
                                    
                                        
                                        
                                             
                                            
                                        
                                    
                                    
                                        
                                                
                                            
                                        
                                    
                                
                            
                        

                    </div>
                </div>
        </div>
        <?php if($_item->teaser || $_item->specification): ?>
            <div class="teaser-product">
                <div class="uk-container uk-container-expand">
                    <?php if($_item->teaser): ?>
                        <div class="param-title uk-text-uppercase">
                            <?php echo app('translator')->getFromJson('frontend.titles.teaser_product'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="uk-grid">
                        <?php if($_item->teaser): ?>
                            <div class="uk-width-expand@m">
                                <div class="teaser">
                                    <?php echo $_item->teaser; ?>

                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($_item->specification): ?>
                            <div class="uk-width-large@xl uk-width-medium@m specification-product">
                                <?php $__currentLoopData = $_item->specification; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_specification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="name">
                                        <?php echo $_specification[0]; ?>

                                    </div>
                                    <div class="text">
                                        <?php echo $_specification[1]; ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
<!--     <div class="card-descriptions uk-margin-medium-top">
        <div class="uk-container">
            <?php if($_item->body): ?>
                <div class="">
                    <?php echo $_item->body; ?>

                </div>
            <?php endif; ?>
            <?php if($_item->relatedFiles && $_item->relatedFiles->isNotEmpty()): ?>
                <div class="entity-files">
                    <?php echo $__env->make('frontend.default.partials.entity_files', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div> -->
</div>
</section>
    <?php echo $__env->make('frontend.default.shops.product_related', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if($_item->body): ?>
        <section class="seo__text">
            <div class="container">
                <?php echo $_item->body; ?>

            </div>
        </section>
    <?php endif; ?>
<script type="text/javascript">
    window.product_info = <?php echo json_encode($_item->productOrder['product']); ?>;
    window.additionally_ingredients = <?php echo json_encode($_item->productOrder['items']); ?>;
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('edit_page'); ?>
    <?php if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product']): ?>
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>
                        <?php if($_locale == DEFAULT_LOCALE): ?>
                            <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                      class="uk-margin-small-right"></span>редактировать', 'oleus.shop_products.edit',
                            ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' =>
                            'uk-link-primary']]); ?>
                        <?php else: ?>
                            <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                      class="uk-margin-small-right"></span>редактировать',
                            'oleus.shop_products.translate', ['p' => ['shop_product' => $_item->id, 'locale' =>
                            $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']]); ?>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>

<script src="/template/js/jquery.inputmask.bundle.min.js"
        type="text/javascript"></script>

    <script type="text/javascript">
        if (typeof fbq == '') {
            var a = {};
            if (typeof FbData == 'object') attr = Object.assign(a, FbData);
            a.content_type = 'product';
            a.content_category = <?php echo $_item->cat; ?>;
            a.content_ids = '<?php echo e($_item->sku); ?>';
            a.value = <?php echo e($_item->price['view_price'] ? $_item->price['view'][0]['format']['price'] : NULL); ?>;
            a.currency = 'UAH';
            fbq('track', 'ViewContent', a);
        }
        <?php if($_item->_eCommerce->isNotEmpty()): ?>
        if (typeof gtag == "function") {
            gtag("event", "view_item", {items: <?php echo $_item->_eCommerce->toJson(); ?> });
        }
        <?php endif; ?>
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.default.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/product.blade.php ENDPATH**/ ?>