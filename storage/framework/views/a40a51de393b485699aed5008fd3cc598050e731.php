<?php
    $_device_type = $_wrap['device']['type'];
?>



<?php $__env->startSection('content'); ?>
        <script>
            window.catalogViewProducts = [];

            function catalogViewPush(p) {
                for (const [k, v] of Object.entries(p)) {
                    if (window.catalogViewProducts[k] == undefined) window.catalogViewProducts[k] = v
                }
            }

            <?php if($_item->_eCommerce->isNotEmpty()): ?>
            if (typeof gtag == "function") {
                gtag("event", "view_item_list", {items: <?php echo $_item->_eCommerce->toJson(); ?> })
            }
            <?php endif; ?>
        </script>
<?php echo menu_render('2'); ?>
<section class="catalog__wrapper">
    <div class="container">
        <h2><?php echo $_item->title; ?></h2>
            
            
            
            
            
            
            
            



                
                        <!-- <div id="uk-items-list-title"
                             class="uk-flex uk-flex-middle">
                            <?php if($_item->preview_fid): ?>
                                <div class="uk-margin-right uk-position-relative icon-preview-fid">
                                <?php echo image_render($_item->_preview, '', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE, 'uk-cover' => TRUE]]); ?>

                                </div>
                            <?php endif; ?>
                            <h1 class="title-02 uk-position-relative <?php if($_item->filterPage): ?> filter-page-title <?php endif; ?>">
                              <?php echo $_wrap['page']['title']; ?>

                            </h1>
                        </div> -->
                


                <section class="filter">
                    <?php if($_item->_items->isNotEmpty()): ?>
                        <?php echo $_item->sortOutput; ?>

                    <?php endif; ?>


                    <?php if($_item->filterOutput): ?>
                        <div id="uk-items-list-filter">
                            <?php echo $_item->filterOutput; ?>

                        </div>
                    <?php endif; ?>
                </section>
           <!--  <div class="uk-grid">
                <div class="uk-width-auto">
                </div>
                <div class="uk-width-expand">
                    <div class="filter-catalog">
                        <?php if($_item->filterOutput): ?>
                            <div id="uk-items-list-filter" class="uk-margin-medium-bottom open">
                            </div>
                        <?php endif; ?>
                   </div>
                </div>
            </div> -->
            <div class="tabs__content active">
                    
                    <?php if($_item->_items->isNotEmpty()): ?>
                        
                        <?php if($_item->viewItem == 'module'): ?>
                            <div class="wrapper" id="uk-items-list">
                                <?php echo $_item->productOutput; ?>

                            </div>
                        <?php else: ?>
                            <div class="wrapper" id="uk-items-list">
                                <?php echo $_item->productOutput; ?>

                            </div>
                        <?php endif; ?>
                        <?php if(method_exists($_item->_items, 'links')): ?>
                            <div class="pagination">
                            <?php echo $_item->_items->links('frontend.default.partials.pagination'); ?>

                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="uk-alert uk-alert-warning">
                            <?php echo app('translator')->getFromJson('frontend.no_items'); ?>
                        </div>
                    <?php endif; ?>
            </div>
    </div>
</section>
    <?php if($_item->body): ?>
        <section class="seo__text">
            <div class="container">
                <?php echo $_item->body; ?>

            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('edit_page'); ?>
    <?php if(isset($_accessEdit['shop_category']) && $_accessEdit['shop_category']): ?>
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>

                        <?php if($_item->filterPage === TRUE): ?>
                            <?php echo _l('<span uk-icon="icon: add; ratio: .7"
                                      class="uk-margin-small-right"></span>добавить страницу',
                            'oleus.shop_filter_pages.create', ['p' => ['category' => $_item->id, 'alias' =>
                            request()->path()], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-success']]); ?>
                        <?php elseif($_item->filterPage): ?>
                            <?php if($_locale == DEFAULT_LOCALE): ?>
                                <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_filter_pages.edit', ['p' => ['shop_filter_page' => $_item->filterPage->id],
                                'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']]); ?>
                            <?php else: ?>
                                <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_filter_pages.translate', ['p' => ['shop_filter_page' =>
                                $_item->filterPage->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank',
                                'class' => 'uk-link-primary']]); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($_locale == DEFAULT_LOCALE): ?>
                                <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_categories.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' =>
                                '_blank', 'class' => 'uk-link-primary']]); ?>
                            <?php else: ?>
                                <?php echo _l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                          class="uk-margin-small-right"></span>редактировать',
                                'oleus.shop_categories.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale],
                                'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']]); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="/template/js/vue.js"
        type="text/javascript"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('schema'); ?>
    <script type="application/ld+json">
        <?php echo $_item->schema; ?>

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.default.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/frontend/default/shops/category.blade.php ENDPATH**/ ?>