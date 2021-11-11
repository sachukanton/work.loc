</td>
</tr>
<tr>
    <td></td>
</tr>
<tr>
    <td>
        <?php if($_site_contacts['phones']): ?>
            <?php $__currentLoopData = $_site_contacts['phones']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_phone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span style="margin-right: 10px; color: #000; text-decoration: none;">
                    <?php echo $_phone['original']; ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if($_site_contacts['email']): ?>
            <a style="margin-right: 15px; color: #000; text-decoration: none;"
               href="mailto:<?php echo e($_site_contacts['email']); ?>"
               title="<?php echo e($_site_contacts['email']); ?>">
                <?php echo $_site_contacts['email']; ?>

            </a>
        <?php endif; ?>
    </td>
</tr>
<tr>
    <td>
                <span style="font-size: .8em; color: #aaaaaa;">
                    <?php echo e(str_replace(':year', date('Y'), $_site_data['site_copyright'])); ?>

                </span>
    </td>
</tr>
</table>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/mail/footer.blade.php ENDPATH**/ ?>