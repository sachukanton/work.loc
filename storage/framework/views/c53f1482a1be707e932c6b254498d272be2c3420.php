<script>
    window.catalogFilterParam = <?php echo json_encode($_filter); ?>;
    window.catalogCatalogUrl = "<?php echo $_category->generate_url; ?>";
</script>

<?php
    $_device_type = wrap()->get('device.type');
?>

<?php if($_filter): ?>
    <catalog-filter-component :refresh="refreshFilter"></catalog-filter-component>
<?php endif; ?>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/filter.blade.php ENDPATH**/ ?>