<?php
    $_mb = ModalBanner::getFirst();
?>
<?php if($_mb): ?>
    <script>
        var m = UIkit.modal('<?php echo $_mb; ?>');
        var nd = new Date();
        var ed = new Date(nd.getFullYear(), nd.getMonth(), nd.getDate(), 12, nd.getMinutes() + 2, 0);
        m.show();
        $('body').delegate(`#modal-banner`, 'hidden', function (event) {
            if (event.target === event.currentTarget) m.$destroy(true);
            document.cookie = "modalBanner=1; expires=" + ed.toUTCString() + ";";
            console.log(ed, "modalBanner=1; expires=" + ed.toUTCString() + ";");
        });
    </script>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/partials/modal_banner.blade.php ENDPATH**/ ?>