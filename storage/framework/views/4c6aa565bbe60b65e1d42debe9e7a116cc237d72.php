<?php if($_item->_items): ?>
<section class="advantages">
    <div class="container">
        <?php if(isset($_accessEdit['block']) && $_accessEdit['block']): ?>
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                <?php if($_locale == DEFAULT_LOCALE): ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
                <?php else: ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.blocks.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="wrapper">
             <?php $__currentLoopData = $_item->_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_advantage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="advantages__item">
                <?php echo image_render($_advantage->_icon, 'thumb_250'); ?>

                <div class="advantages__item--info">
                    <h6><?php echo $_advantage->title; ?></h6>
                    <?php if($_advantage->sub_title): ?>
                        <p><?php echo $_advantage->sub_title; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/blocks/advantage_1.blade.php ENDPATH**/ ?>