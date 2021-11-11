<?php if(session('notice')): ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.notification("<?php echo session('notice.message'); ?>", {
                status: '<?php echo e(session('notice.status', 'primary')); ?>',
                pos: 'top-center'
            });
        });
    </script>
<?php endif; ?>
<?php if(session('notices')): ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            <?php $__currentLoopData = session('notices'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_notice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            UIkit.notification("<?php echo $_notice['message']; ?>", {
                status: '<?php echo e($_notice['status'] ?? 'primary'); ?>',
                pos: 'top-center'
            });
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        });
    </script>
<?php endif; ?>
<?php if(session('modal')): ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.modal('<div class="uk-modal uk-flex-top" id="modal-alert"><div class="uk-modal-dialog uk-margin-auto-vertical uk-border-rounded alert-<?php echo e(session('modal.status')); ?>">' +
                '<?php echo session('modal.message'); ?></div></div>', {}).show();
        });
    </script>
<?php endif; ?>
<?php if(session('commands')): ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                var $commands = <?= session('commands') ?>;
                for (var $i = 0; $i < $commands.length; ++$i) {
                    command_action($commands[$i]);
                }
            }, 500);
        });
    </script>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/partials/notice.blade.php ENDPATH**/ ?>