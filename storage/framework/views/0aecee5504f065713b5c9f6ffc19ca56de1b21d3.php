<div class="file uk-position-relative">
    <div uk-grid
         class="uk-grid-collapse">
        <?php if($file->filemime == 'image/svg+xml'): ?>
            <div class="uk-position-relative uk-width-1-1 uk-height-1-1 uk-image">
                <input type="hidden"
                       name="<?php echo e($_options['field']); ?>[<?php echo e($file->id); ?>][id]"
                       value="<?php echo e($file->id); ?>">
                <button type="button"
                        data-fid="<?php echo e($file->id); ?>"
                        uk-icon="icon: delete"
                        class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-position-absolute uk-position-z-index"
                        style="top: 5px; right: 5px;">
                </button>
                <?php echo image_render($file, 'thumb_image', ['attributes' => ['uk-svg' => TRUE, 'height' => '100']]); ?>

            </div>
        <?php else: ?>
            <input type="hidden"
                   name="<?php echo e($_options['field']); ?>[<?php echo e($file->id); ?>][id]"
                   value="<?php echo e($file->id); ?>">
            <div class="uk-width-expand">
                <?php echo _l(str_limit($file->filename, 40), $file->base_url, ['attributes' => ['target' => '_blank', 'title' => $file->filename]]); ?>
                (<?php echo e($file->filesize); ?>KB)
            </div>
            <div class="uk-width-auto">
                <div class="uk-button-group">
                    <button type="button"
                            uk-icon="icon: info"
                            data-path="<?php echo e(_r('ajax.file.update', ['file' => $file->id])); ?>"
                            class="uk-button uk-button-icon uk-button-primary uk-button-small use-ajax">
                    </button>
                    <button type="button"
                            data-fid="<?php echo e($file->id); ?>"
                            uk-icon="icon: delete"
                            class="uk-button uk-button-icon uk-button-danger uk-file-remove-button uk-button-small">
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/partials/file_preview.blade.php ENDPATH**/ ?>