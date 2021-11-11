<?php if($_form->modal): ?>
    <div class="uk-modal-body">
        <button class="uk-modal-close-outside"
                type="button"
                uk-close></button>
        <div class="shortcut-form modal uk-padding-remove">
            <?php endif; ?>
            <?php if($_form->prefix): ?>
                <?php echo $_form->prefix; ?>

            <?php endif; ?>
            <form method="post"
                  enctype="multipart/form-data"
                  id="<?php echo e($_form->id); ?>"
                  action="<?php echo e($_form->action); ?>"
                  class="uk-form <?php echo e($_form->form_class ? " {$_form->form_class}" : NULL); ?><?php echo e($_form->ajax ? ' use-ajax' : NULL); ?>">
                <input type="hidden"
                       name="form"
                       value="<?php echo e($_form->id); ?>">
                <?php if($_form->title): ?>
                    <h2 class="title-02 uk-position-relative uk-position-z-index uk-margin-remove-top">
                        <?php echo $_form->title; ?>

                    </h2>
                <?php endif; ?>
                <?php $__currentLoopData = $_form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $_field; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($_form->body): ?>
                    <div class="uk-margin uk-text-small uk-text-muted">
                        <?php echo $_form->body; ?>

                    </div>
                <?php endif; ?>
                <div class="uk-form-action uk-clearfix">
                    <?php if($_form->buttons): ?>
                        <?php $__currentLoopData = $_form->buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $_button; ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <button type="submit"
                                class="uk-button btn-submit uk-position-relative uk-flex-middle uk-margin-auto <?php echo e($_form->button_send_class); ?>"
                                value="1"
                                name="send_form">
                            <?php echo e($_form->button_send_title ?: trans('Send the Form')); ?>

                        </button>
                    <?php endif; ?>
                </div>
                <?php echo csrf_field(); ?>

                <?php echo method_field('POST'); ?>

                <input type="hidden"
                       name="form_id"
                       value="<?php echo e($_form->id); ?>">
            </form>
            <?php if($_form->suffix): ?>
                <?php echo $_form->suffix; ?>

            <?php endif; ?>
            <?php if($_form->modal): ?>
        </div>
    </div>
    </div>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/forms/form_generate.blade.php ENDPATH**/ ?>