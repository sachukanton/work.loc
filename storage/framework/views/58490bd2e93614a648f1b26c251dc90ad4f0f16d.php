<div class="uk-position-relative">
    <ul <?php echo e($_item->style_id ? "id=\"{$_item->style_id}\"" : NULL); ?> class="uk-nav uk-flex uk-flex-wrap <?php echo e($_item->style_class); ?>">
        <?php $__currentLoopData = $_item->menu_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_menu_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('frontend.default.menus.menu_4_item', ['_item' => $_menu_item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
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
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/menus/menu_4.blade.php ENDPATH**/ ?>