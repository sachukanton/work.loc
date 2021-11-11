<?php
    global $wrap;
    $_device_type = $_wrap['device']['type'];
?>



<?php $__env->startSection('content'); ?>
        

        <?php echo slider_render('1'); ?>
        <?php echo menu_render('2'); ?>
        <?php echo advantage_block_render('1'); ?>

        <?php if(isset($_others['recommended_front']) && $_others['recommended_front']): ?>
            <?php echo $_others['recommended_front']['object']; ?>

        <?php endif; ?>
        <?php if(isset($_others['new']) && $_others['new']): ?>
            <?php echo $_others['new']['object']; ?>

        <?php endif; ?>


<!--         <?php echo menu_render('3'); ?>
        <?php echo App\Models\Structure\Node::getNodeSlider(); ?> -->

        
        
        
        
            
        
        

            
                    
                    

            
            
            

            
                    
                    
            
                    
                    
            
                    
                    

        
        <section class="seo__text" style="background-image: url(/template/images/bg-bottom.png); background-position: top; background-size: contain;">
            <div class="container">
                    <h2>
                       <?php echo $_wrap['page']['title']; ?>

                    </h2>
                    <?php if($_item->sub_title): ?>
                        <h3>
                            <?php echo $_item->sub_title; ?>

                        </h3>
                    <?php endif; ?>
                    <?php if($_item->body): ?>
                        <?php echo $_item->body; ?>

                    <?php endif; ?>
                </div>
            </div>
        </section>
<?php $__env->stopSection(); ?>


    
        
            
                    
                
            
            
                 
                
                    
                        
                            
                        
                            
                        
                    
                
            
        
    


<?php echo $__env->make('frontend.default.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/pages/front.blade.php ENDPATH**/ ?>