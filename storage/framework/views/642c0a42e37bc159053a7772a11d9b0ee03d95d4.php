<div class="blogs__item">
    <a href="<?php echo e($_item->generate_url); ?>"
           rel="nofollow">
                <?php if($_item->preview_fid): ?>
                    <div class="blogs__item--img">
                        <?php echo $_item->_preview_asset('nodeTeaser_400_300', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title)]]); ?>

                    </div>
                <?php endif; ?>
    </a>
    <div class="node-teaser">
        <h6>
            <a href="<?php echo e($_item->generate_url); ?>"
               rel="nofollow">
            <?php echo $_item->title; ?>

            </a>
        </h6>
        <span class="blogs__item_date">
            <?php echo e($_item->published_at->format('d.m.Y')); ?>

        </span>
        
            
        
        
        
        
        
        
    </div>
    <?php if(isset($_accessEdit['node']) && $_accessEdit['node']): ?>
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <?php if($_locale == DEFAULT_LOCALE): ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.nodes.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php else: ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.nodes.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/nodes/node_teaser.blade.php ENDPATH**/ ?>