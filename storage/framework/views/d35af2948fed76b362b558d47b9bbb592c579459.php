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
    <div class="uk-form-controls <?php echo e($params->get('item_class')); ?>">
        <textarea id="<?php echo e($params->get('id')); ?>"
                  name="<?php echo e($params->get('name')); ?>"
                  <?php echo $params->get('attributes') ? " {$params->get('attributes')}" : ''; ?>

                  class="uk-textarea <?php echo e(($class = $params->get('class')) ? " {$class}" : ''); ?><?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>"><?php echo $params->get('selected'); ?></textarea>
        <?php if($params->get('class') == 'uk-codeMirror'): ?>
            <div class="uk-text-right uk-margin-small-top">
                <button type="button"
                        data-id="<?php echo e($params->get('id')); ?>"
                        class="uk-button uk-button-color-amber uk-button-small uk-button-use-ckEditor">
                    <span uk-icon="text_format"></span>
                    Редактор текста
                </button>
                <button type="button"
                        data-id="<?php echo e($params->get('id')); ?>"
                        class="uk-button uk-button-color-indigo uk-button-small uk-button-use-code-mirror">
                    <span uk-icon="settings_ethernet"></span>
                    Редактор кода
                </button>
            </div>
        <?php endif; ?>
        <?php if($help = $params->get('help')): ?>
            <div class="uk-help-block">
                <?php echo $help; ?>

            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/fields/textarea.blade.php ENDPATH**/ ?>