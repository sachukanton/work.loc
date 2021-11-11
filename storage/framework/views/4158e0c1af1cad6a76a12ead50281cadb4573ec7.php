<?php
    $files = ($_files = session($params->get('old'))) ? json_decode($_files) : (($_files = $params->get('values')) ? $_files : NULL);
?>
<div class="uk-margin"
     id="<?php echo e($params->get('id')); ?>-form-field-box">
    <?php if($label = $params->get('label')): ?>
        <label for="<?php echo e($params->get('id')); ?>"
               class="uk-form-label"><?php echo $label; ?>

            <?php if($params->get('required')): ?>
                <span class="uk-text-danger">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    <?php if($params->get('ajax_url')): ?>
        <div
            class="uk-display-block uk-form-controls-file uk-form-controls <?php echo e($params->get('multiple') ? 'uk-multiple-file' : 'uk-one-file'); ?><?php echo e(!$params->get('multiple') && $files ? ' loaded-file' : ''); ?>"
            data-view="<?php echo e($params->get('upload_view')); ?>">
            <div class="uk-width-1-1 uk-position-relative">
                <input type="hidden"
                       name="<?php echo e($params->get('name')); ?>">
                <div class="uk-preview">
                    <?php if($params->get('upload_view') == 'gallery'): ?>
                        <div class="uk-grid uk-grid-small uk-child-width-1-3"
                             uk-sortable="handle: .uk-sortable-handle">
                            <?php endif; ?>
                            <?php if($files): ?>
                                <?php
                                    $_options = [
                                    'field' => $params->get('name'),
                                    'view' => $params->get('upload_view')
                                    ];
                                ?>
                                <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo preview_file_render($file, $_options); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php if($params->get('upload_view') == 'gallery'): ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="uk-field uk-text-right">
                    <div
                        class="js-upload uk-placeholder uk-text-center uk-border-rounded<?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>"
                        id="<?php echo e($params->get('id')); ?>">
                    <span uk-icon="icon: cloud_upload"
                          class="uk-text-muted"></span>
                        <span class="uk-text-middle uk-text-small">
                        Перетяните файл или воспользуйтесь
                    </span>
                        <?php ($_upload_allow = $params->get('upload_allow')); ?>
                        <div data-url="<?php echo e($params->get('ajax_url')); ?>"
                             data-allow="<?php echo e($_upload_allow); ?>"
                             data-field="<?php echo e($params->get('name')); ?>"
                             data-multiple="<?php echo e($params->get('multiple') ? 1 : 0); ?>"
                             data-view="<?php echo e($params->get('upload_view')); ?>"
                             class="uk-field file-upload-field<?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>"
                             uk-form-custom>
                            <input type="file"<?php echo e($params->get('multiple') ? ' multiple' : ''); ?>>
                            <span class="uk-link uk-text-lowercase uk-text-small">
                            выбором
                        </span>
                        </div>
                        <?php ($_upload_allow_view = str_replace('*.(', '.', str_replace(')', '', str_replace('|', ' .', $_upload_allow)))); ?>
                        <div class="uk-text-small uk-text-muted">
                            В поле можно загрузить файлы следующих форматов:
                            <div class="uk-text-bold"><?php echo e($_upload_allow_view); ?></div>
                            <?php if($help = $params->get('help')): ?>
                                <span class="uk-help-block uk-display-block"><?php echo $help; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="uk-progress-preloader">
                    <div class="uk-progress-loader"></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="uk-form-controls">
            <div uk-form-custom="target: true"
                 class="uk-width-1-1">
                <input type="file"
                       name="<?php echo e($params->get('name')); ?>"
                       value="<?php echo e($params->get('selected')); ?>"
                       <?php echo e($params->get('multiple') ? 'multiple' : NULL); ?>

                       autocomplete="off">
                <input
                    class="uk-input<?php echo e(($class = $params->get('class')) ? " {$class}" : ''); ?><?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>"
                    id="<?php echo e($params->get('id')); ?>"
                    type="text"
                    <?php echo $params->get('attributes') ? " {$params->get('attributes')}" : ''; ?>

                    disabled>
            </div>
            <?php if($help = $params->get('help')): ?>
                <div class="uk-help-block">
                    <?php echo $help; ?>

                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/fields/file.blade.php ENDPATH**/ ?>