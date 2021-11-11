<?php if($_item instanceof \App\Models\Shop\Product): ?>
    <?php
        global $wrap;
        $_mark = $_item->mark[0] ?? NULL;
        $_mark_hit = $_item->paramOptions[5] ?? NULL;
        if($_mark_hit){
        foreach ($_mark_hit['options'] as $_option_id => $_option_item){
        $_mark_hit_id = 'mark-hit-' . $_option_id;
        }
        }
        $_device_type = $wrap['device']['type'] ?? 'pc';
    ?>
    <div class="swiper-slide">
        <div class="category__open_item dom-item-card-product-<?php echo e($_item->modify); ?>">
            <div class="category__open_item--img <?php echo e($_mark_hit_id ?? NULL); ?> product-id-<?php echo e($_item->id); ?> <?php if($_item->price['count_in_basket'] >= 1): ?> add-basket <?php endif; ?>">
                    <div>
                        <?php if(($_param = ($_item->paramOptions[5] ?? NULL))): ?>
                            <?php $__currentLoopData = $_param['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_option_id => $_option_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="product-marks-hit"
                                     style="background-color: <?php echo e('#' . $_option_item['attribute']); ?>">
                                    <?php echo $_option_item['title']; ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(($_param = ($_item->paramOptions[1] ?? NULL))): ?>
                            <div class="tag">
                                <?php $__currentLoopData = $_param['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_option_id => $_option_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $_option_item['icon']; ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="preview-product uk-margin-auto uk-flex uk-flex-center uk-flex-middle">
                        <a href="<?php echo e($_item->generate_url); ?>"
                           rel="nofollow">

                            <div id="product-<?php echo e($_item->id); ?>"
                                 class="shop-product-change-images">
                                <?php if($_item->preview_fid || $_item->full_fid): ?>


                                    <?php if($_item->full_fid): ?>
                                    <?php echo image_render($_item->_preview_full, 'productTeaser_344_319', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]); ?>

                                    <?php else: ?>
                                    <?php if($_item->preview_fid): ?>
                                    <?php echo $_item->_preview_asset('productTeaser_344_319', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]); ?>

                                    <?php endif; ?>
                                    <?php endif; ?>


                                    <?php if($_device_type == 'mobile'): ?>
                                        <div class="preview-fid-mb uk-flex uk-flex-center uk-flex-middle uk-position-relative">
                                            <?php if($_item->preview_fid): ?>
                                                <?php echo $_item->_preview_asset('productTeaser_150_100', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE, 'uk-cover' => TRUE]]); ?>

                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="preview-fid uk-flex uk-flex-center uk-flex-middle uk-position-relative">
                                            <?php if($_item->preview_fid): ?>
                                                <?php echo $_item->_preview_asset('productTeaser_300_200', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE, 'uk-cover' => TRUE]]); ?>

                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo image_render(NULL, 'productTeaser_320_320', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]); ?>

                                <?php endif; ?>
                                
                                
                                
                                
                                
                            </div>
                            

                        </a>
                    </div>
            </div>
            <div class="category__open_item--info">
                <div>
                    <h6 class="open_title"><?php echo _l(str_limit(strip_tags($_item->title), 50), $_item->generate_url, ['attributes' => ['title' =>
                    strip_tags(str_replace([
                    "'",
                    '"'
                    ], '', $_item->title))]]); ?></h6>
                </div>
                <div class="category__open_item--more">
                    <div class="wrapperes">
                        <p>
                        <?php if(($_param = ($_item->paramOptions[4] ?? NULL))): ?>
                           
                                <?php echo $_param['options'] . ' ' . $_param['unit']; ?>

                                -&nbsp;
                            
                        <?php endif; ?>
                        <?php if(($_param = ($_item->paramOptions[3] ?? NULL))): ?>
                            
                                <?php echo $_param['options'] . ' ' . $_param['unit']; ?>

                           
                        <?php endif; ?>
                        <?php if(($_param = ($_item->paramOptions[7] ?? NULL))): ?>
                           
                                <?php echo $_param['options'] . ' ' . $_param['unit']; ?>

                            
                        <?php endif; ?>
                        </p>

                        <div class="price">
                            <?php if($_item->price['view_price']): ?>
                            <?php if(count($_item->price['view']) > 1): ?>
                                <div class="old_price"><?php echo $_item->price['view'][0]['format']['view_price']; ?></div>
                                <div class="real-price">
                                    <?php echo $_item->price['view'][1]['format']['view_price_2']; ?>

                                </div>
                            <?php else: ?>
                                <div class="real-price">
                                    <?php echo $_item->price['view'][0]['format']['view_price_2']; ?>

                                </div>
                            <?php endif; ?>
                            <?php else: ?>
                                <div class="product-not-available">
                                    <?php echo app('translator')->getFromJson('frontend.not_available'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if(($_param = ($_item->paramOptions[2] ?? NULL))): ?>                    
                            <?php
                                $_param_values = NULL;
                                foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                            ?>
                            <p>
                                <?php echo e(str_limit(implode(', ', $_param_values),60)); ?>

                            </p>
                    <?php endif; ?>
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
                <div class="btn__wrapper">
                    <button type="button"
                            data-path="<?php echo e(_r('ajax.shop_buy_one_click')); ?>"
                            data-product="<?php echo e($_item->id); ?>"
                            class="btn--white use-ajax">
                        <?php echo e(variable('one_click')); ?>

                    </button>
                    <button type="button"
                        data-path="<?php echo e(_r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']])); ?>"
                        data-type="teaser"
                        data-spicy="<?php echo e((int) $_item->is_spicy); ?>"
                        class="btn btn-cart-product-<?php echo e($_item->id); ?> use-ajax">
                        <svg>
                            <use xlink:href="#bike"></use>
                        </svg>
                        <?php echo e(variable('cart')); ?>

                    </button>
                </div>
            </div>
        </div>
        <?php if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product']): ?>
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right uk-position-z-index">
                <?php if($_locale == DEFAULT_LOCALE): ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.edit', ['p' =>
                    ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block
                    uk-line-height-1']]); ?>
                <?php else: ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.translate', ['p' =>
                    ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' =>
                    'uk-display-block uk-line-height-1']]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($_item->js_modification_items && $_item->modification_items && $_item->modification_items->count() > 1): ?>
            <script>
                if (typeof catalogViewPush == "function") catalogViewPush(<?php echo json_encode($_item->js_modification_items); ?>);
            </script>
        <?php endif; ?>
    </div>
               <!--  <?php if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product']): ?>
                    <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right uk-position-z-index">
                        <?php if($_locale == DEFAULT_LOCALE): ?>
                            <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.edit', ['p' =>
                            ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block
                            uk-line-height-1']]); ?>
                        <?php else: ?>
                            <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.translate', ['p' =>
                            ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' =>
                            'uk-display-block uk-line-height-1']]); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?> -->
        
<?php elseif($_item): ?>
    <div class="category__open_item banner">
        <?php echo $_item->link ? '<a href="'. _u($_item->link) .'" '. ($_item->link_attributes ?: NULL) .' class="">' : NULL; ?>

        <?php if($_item->background_fid): ?>
            <?php echo $_item->_background_asset(NULL, ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]); ?>

        <?php else: ?>
            <?php echo content_render($_item); ?>

        <?php endif; ?>
        <?php echo $_item->link ? '</a>' : NULL; ?>

    </div>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/product_teaser.blade.php ENDPATH**/ ?>