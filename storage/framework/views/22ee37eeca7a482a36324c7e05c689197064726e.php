<?php
    $selected = $params->get('selected');
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
    <div class="uk-form-controls<?php echo e(($class = $params->get('class')) ? " " : ''); ?><?php echo e(($error = $params->get('error')) ? ' uk-form-danger' : ''); ?>"
         id="<?php echo e($params->get('id')); ?>">
        <?php $__currentLoopData = $params->get('values'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item_key => $item_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="uk-margin-small">
                <label for="<?php echo e($params->get('id') ."-{$item_key}"); ?>"
                       class="uk-display-block">
                    <input name="<?php echo e($params->get('name')); ?>"
                           type="radio"
                           id="<?php echo e($params->get('id')."-{$item_key}"); ?>"
                           class="uk-radio<?php echo e(($class = $params->get('class')) ? " {$class}" : ''); ?>"
                           data-key="<?php echo e($item_key); ?>"
                           value="<?php echo e($item_key); ?>"
                        <?php echo $params->get('attributes') ? " {$params->get('attributes')}" : ''; ?>

                        <?php echo e($selected == $item_key ? ' checked' : ''); ?>>
                    <?php if(is_array($item_value)): ?>
                        <span class="uk-display-inline-block"><?php echo $item_value[0]; ?></span>
                        <?php if(isset($item_value[1])): ?>
                            <span class="uk-help-form-label"><?php echo $item_value[1]; ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="uk-display-inline-block"><?php echo $item_value; ?></span>
                    <?php endif; ?>
                </label>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($help = $params->get('help')): ?>
            <div class="uk-help-block">
                <?php echo $help; ?>

            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/fields/radio.blade.php ENDPATH**/ ?>