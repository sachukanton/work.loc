<?php if($_item->relatedProduct->isNotEmpty()): ?>
    <section class="more__items">
        <div class="container">
                <h4><?php echo e(variable('recomender')); ?></h4>
            <div class="wrapper">
                
                    
                                <?php $__currentLoopData = $_item->relatedProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                        <?php echo $__env->make('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                    
                                         
                                
                                
                                    
                                         
                                
                    
                    
                        
                        
                            
                                
                                    
                                
                            
                        
                        
                            
                                
                                
                                    
                                        
                                    
                                
                            
                        
                    
                
                    
                        
                            
                                
                                    
                                
                            
                        
                    
                    
                        
                        
                            
                                
                                    
                                
                            
                        
                        
                            
                                
                                
                                    
                                        
                                    
                                
                            
                        
                    
                
                
                
                
                
                
            </div>
        </div>
    </section>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/product_related.blade.php ENDPATH**/ ?>