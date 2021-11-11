<?php
    $_device_type = $_wrap['device']['type'];
?>
<?php if($_item->consistProduct->isNotEmpty()): ?>
    <div class="open_item_wrapper">
        <div class="swiper-container sets_item">
            <div class="swiper-wrapper">
                
                            <?php $__currentLoopData = $_item->consistProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                    <?php echo $__env->make('frontend.default.shops.product_teaser_consist', ['_item' => $_product, '_class' => ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                        
                        
                            
                                
                                    
                                
                            
                        
                        
                            
                                
                                
                                    
                                        
                                    
                                
                            
                        
                    
                
                    
                        
                            
                                
                                    
                                
                            
                        
                        
                            
                                 
                        
                        
                            
                                 
                        
                    
                    
                        
                        
                            
                                
                                    
                                
                            
                        
                        
                            
                                
                                
                                    
                                        
                                    
                                
                            
                        
                    
                
            </div>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/product_consist.blade.php ENDPATH**/ ?>