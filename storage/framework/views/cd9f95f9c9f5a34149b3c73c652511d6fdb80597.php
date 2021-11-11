

<?php $__env->startSection('content'); ?>
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin">
                <?php echo $_wrap['seo']['title']; ?>

            </h1>
        </div>
        <form class="uk-form uk-form-stacked uk-width-1-1 uk-margin-medium-bottom"
              method="POST"
              enctype="multipart/form-data"
              action="<?php echo e(_r('oleus.settings', ['page' => $_form->route_tag])); ?>">
            <?php echo e(csrf_field()); ?>

            <?php echo e(method_field('POST')); ?>

            <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
                <div class="uk-card-header uk-text-right"
                     uk-sticky="animation: uk-animation-slide-top; top: 80">
                    <?php if($_form->buttons): ?>
                        <?php $__currentLoopData = $_form->buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $_button; ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <button type="submit"
                            name="save"
                            value="1"
                            class="uk-button uk-button-success uk-text-uppercase">
                        Сохранить настройки
                    </button>
                </div>
                <div class="uk-card-body">
                    <?php if($errors->any()): ?>
                        <div class="uk-alert uk-alert-danger">
                            <ul class="uk-list">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="uk-margin-remove"><?php echo e($_error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if($_form->tabs): ?>
                        <div class="uk-grid-match"
                             uk-grid>
                            <div class="uk-width-1-4">
                                <ul class="uk-tab uk-tab-left"
                                    uk-tab="connect: #uk-tab-body; animation: uk-animation-fade; swiping: false;">
                                    <?php $__currentLoopData = $_form->tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($tab): ?>
                                            <li class="<?php echo e($loop->index == 0 ? 'uk-active' : ''); ?>">
                                                <a href="#"><?php echo e($tab['title']); ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($_form->seo) && $_form->seo): ?>
                                        <li>
                                            <a href="#"><?php echo app('translator')->getFromJson('others.tab_meta_tags'); ?></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="uk-width-3-4">
                                <ul id="uk-tab-body"
                                    class="uk-switcher uk-margin">
                                    <?php $__currentLoopData = $_form->tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($tab): ?>
                                            <li class="<?php echo e($loop->index == 0 ? 'uk-active' : ''); ?>">
                                                <?php $__currentLoopData = $tab['content']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php echo $content; ?>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($_form->seo) && $_form->seo): ?>
                                        <li>
                                            <?php echo $__env->make('backend.fields.fields_group_meta_tags', compact('_item'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php elseif($_form->contents): ?>
                        <?php $__currentLoopData = $_form->contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($content): ?>
                                <?php echo $content; ?>

                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($_form->seo): ?>
                            <?php echo $__env->make('backend.fields.fields_group_meta_tags', compact('_item'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/forms/form_settings.blade.php ENDPATH**/ ?>