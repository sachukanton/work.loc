<?php
    $_breadcrumbs = $_items ?? $_wrap['page']['breadcrumb'];
?>
<?php if($_breadcrumbs): ?>
    <section class="breadcrambs" id="breadcrumbs">
        <div class="container">
            <?php if($_device_type == 'pc'): ?>
                <?php $__currentLoopData = $_breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$loop->last): ?>
                            <a href="<?php echo e($_item['url']); ?>">
                                <?php echo $_item['name']; ?>

                            </a>
                        <?php else: ?>
                        <span>
                            <?php echo $_item['name']; ?>

                        </span>
                        <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <a class="to_main" href="<?php echo e(LaravelLocalization::getLocalizedURL(LaravelLocalization::getCurrentLocale(), '/')); ?>"><?php echo variable('go_main'); ?></a>
            <?php endif; ?>
        </div>
    </section>
    <?php
        $_i = 0;
        $_breadcrumbs_items = [];
        foreach ($_breadcrumbs as $_item){
            $_i++;
            $_breadcrumbs_items[] = [
            "@type"=> "ListItem",
                "position"=> $_i,
                "name"=>  $_item['name'],
                "item"=> config('app.url') . $_item['url']
            ];
        }
        $_breadcrumbs = json_encode([
            "@context" => "https://schema.org/",
            "@type" => "BreadcrumbList",
            "itemListElement" => [
                $_breadcrumbs_items
            ]
        ]);
    ?>
    <script type="application/ld+json">
        <?php echo $_breadcrumbs; ?>

    </script>
<?php endif; ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/partials/breadcumb.blade.php ENDPATH**/ ?>