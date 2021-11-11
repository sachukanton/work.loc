<?php if($_item->prefix): ?>
    <?php echo $_item->prefix; ?>

<?php endif; ?>
<form method="post"
      enctype="multipart/form-data"
      id="<?php echo e($_form_data->form_id); ?>"
      action="<?php echo e(_r('ajax.submit_form', ['form' => $_item->id])); ?>"
      class="use-ajax uk-form uk-padding-small uk-position-relative<?php echo e($_item->style_class ? " {$_item->style_class}" : NULL); ?>">
    <?php echo csrf_field(); ?>

    <?php echo method_field('POST'); ?>

    <input type="hidden"
           name="form_index"
           value="<?php echo e($_item->render_index); ?>">
    <?php if($_item->hidden_title == 0): ?>
        <h2 class="uk-text-bold uk-heading-divider uk-margin-remove-top">
            <?php echo $_item->title; ?>

        </h2>
        <?php if($_item->sub_title): ?>
            <div class="uk-text-meta uk-margin-bottom uk-text-center">
                <?php echo $_item->sub_title; ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($_form_data->use_steps): ?>
        <?php echo $__env->make('backend.base.form_steps_item', ['_step_item' => $_form_data->steps[$_form_data->first_step], '_form' => $_item, '_form_data' => $_form_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php $__currentLoopData = $_form_data->render_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $_field; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="uk-margin uk-form-action uk-clearfix">
            <button type="submit"
                    class="uk-button uk-button-success uk-float-right <?php echo e($_item->settings->send->class ?? NULL); ?>"
                    value="1"
                    name="send_form">
                <?php echo e($_item->button_send ?: 'Send the Form'); ?>

            </button>
        </div>
    <?php endif; ?>
    <?php if($_item->body): ?>
        <div class="uk-margin uk-text-small uk-text-muted">
            <?php echo $_item->body; ?>

        </div>
    <?php endif; ?>
    <?php if(isset($_accessEdit['form']) && $_accessEdit['form']): ?>
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <?php if($_locale == DEFAULT_LOCALE): ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.forms.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php else: ?>
                <?php echo _l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.forms.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']]); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</form>
<?php if($_item->suffix): ?>
    <?php echo $_item->suffix; ?>

<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/base/form.blade.php ENDPATH**/ ?>