<?php if($_sub_categories): ?>
    <div id="uk-items-list-sub-categories" class="menu-sub-catalog">
        <ul uk-grid class="uk-nav uk-grid-small uk-grid">
            <?php $__currentLoopData = $_sub_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_sub_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="uk-width-1-5@l uk-width-1-3@s">
                    <?php echo _l(str_limit(strip_tags($_sub_category['title']), 40), $_sub_category['alias'], ['attributes' => ['title' => strip_tags($_sub_category['title']), 'class' => 'level-item-1 uk-text-uppercase']]); ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
     </div>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/sub_categories.blade.php ENDPATH**/ ?>