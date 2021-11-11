<?php
    global $wrap;
    $_device_type = $wrap['device']['type'];
?>

<section class="intro">
    <div class="container">
<?php if($_item->_items->isNotEmpty()): ?>
    <div <?php echo e($_item->style_id ? "id=\"{$_item->style_id}\"" : NULL); ?>

             class="swiper-container <?php echo e($_item->style_class ? " {$_item->style_class}" : NULL); ?>">
        <div class="swiper-wrapper">
            <?php $__currentLoopData = $_item->_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="swiper-slide">
                <?php if(!$_slide->hidden_title): ?>
                    <div class="wrapper">
                        <div class="intro__info">
                            <h1><?php echo $_slide->title; ?></h1>
                            <?php if($_slide->body): ?>
                                <?php echo $_slide->body; ?>

                            <?php endif; ?>
                        </div>  
                        <div class="intro__img">
                            <?php echo $_slide->_background_asset(NULL, ['only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title)]]); ?>

                        </div>
                    </div>
                <?php else: ?>
                <div class="wrappers">
                    <div class="intro__img">
                        <?php echo $_slide->_background_asset(NULL, ['only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title)]]); ?>

                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($_item->dotnav): ?>
            <div class="swiper-pagination"></div>
        <?php endif; ?>
        <?php if(isset($_accessEdit['slider']) && $_accessEdit['slider']): ?>
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                <?php if($_locale == DEFAULT_LOCALE): ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.edit', ['p' => ['id'
                    => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block
                    uk-line-height-1']]); ?>
                <?php else: ?>
                    <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.sliders.translate', ['p' =>
                    ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class'
                    => 'uk-display-block uk-line-height-1']]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
    </div>
</section>

<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/sliders/slider_1.blade.php ENDPATH**/ ?>