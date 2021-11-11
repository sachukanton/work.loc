<?php
    global $wrap;
    $_device_type = $wrap['device']['type'];
?>
<?php if($wrap['page']['is_front']): ?>
   <section class="category">
    <div class="container">
        <div class="wrapper">
            <?php $__currentLoopData = $_item->menu_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_menu_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('frontend.default.menus.menu_2_item', ['_item' => $_menu_item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if(isset($_accessEdit['menu']) && $_accessEdit['menu']): ?>
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-position-z-index">
            <?php if($_locale == DEFAULT_LOCALE): ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php else: ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
</section>
<?php else: ?>
    <section class="category category--top">
    <div class="container">
        <div class="wrapper">
           <?php $__currentLoopData = $_item->menu_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_menu_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <?php echo $__env->make('frontend.default.menus.menu_2_item', ['_item' => $_menu_item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if(isset($_accessEdit['menu']) && $_accessEdit['menu']): ?>
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-position-z-index">
            <?php if($_locale == DEFAULT_LOCALE): ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php else: ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.menus.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/menus/menu_2.blade.php ENDPATH**/ ?>