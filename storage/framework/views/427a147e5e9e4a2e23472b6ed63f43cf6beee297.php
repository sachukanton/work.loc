<div class="uk-margin<?php echo e($params->get('required') ? ' uk-form-required' : NULL); ?>"
     id="<?php echo e($params->get('id')); ?>-form-field-box">
    <?php if($label = $params->get('label')): ?>
        <label for="<?php echo e($params->get('id')); ?>"
               class="uk-form-label"><?php echo $label; ?>

            <?php if($params->get('required')): ?>
                <span class="uk-text-danger">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    <div class="uk-form-controls">
        <input type="<?php echo e($params->get('type')); ?>"
               id="<?php echo e($params->get('id')); ?>"
               name="<?php echo e($params->get('name')); ?>"
               value="<?php echo e($params->get('selected')); ?>"
               autocomplete="off"
               <?php echo $params->get('attributes') ? " {$params->get('attributes')}" : ''; ?>

               class="uk-input<?php echo e(($class = $params->get('class')) ? " {$class}" : ''); ?><?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>">
        <?php if($help = $params->get('help')): ?>
            <div class="uk-help-block">
                <?php echo $help; ?>

            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/fields/text.blade.php ENDPATH**/ ?>