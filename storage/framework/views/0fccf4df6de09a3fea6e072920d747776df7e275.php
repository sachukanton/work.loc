

<?php $__env->startSection('content'); ?>
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove uk-text-uppercase uk-text-thin uk-text-color-teal">
                <?php echo $_wrap['seo']['title']; ?>

            </h1>
        </div>
        <?php if($_items->before): ?>
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                <?php if($_items->before['header']): ?>
                    <div class="uk-card-header">
                        <h2 class="uk-text-uppercase">
                            <?php echo $_items->before['header']; ?>

                        </h2>
                    </div>
                <?php endif; ?>
                <div class="uk-card-body">
                    <?php echo $_items->before['body']; ?>

                </div>
                <?php if($_items->before['footer']): ?>
                    <div class="uk-card-footer uk-text-right">
                        <?php echo $_items->before['footer']; ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
            <?php if($_items->buttons || $_items->filters): ?>
                <div class="uk-card-header">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-expand">
                            <?php if($_items->filters): ?>
                                <button uk-toggle="target: #items-filter"
                                        class="uk-button uk-button-primary uk-border-rounded uk-margin-small-right uk-text-uppercase"
                                        type="button">
                                    <span uk-icon="icon: filter_list"></span>&nbsp;
                                    Фильтровать
                                </button>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php $__currentLoopData = $_items->buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $_button; ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php if($_items->filters): ?>
                        <div id="items-filter"
                             class="uk-margin-small-top"
                            <?php echo e($_items->use_filters ?: 'hidden'); ?>>
                            <form action=""
                                  method="get">
                                <div class="uk-grid uk-grid-small uk-flex uk-flex-bottom">
                                    <div class="uk-width-expand"
                                         style="border-right: 1px #e4e9f0 solid;">
                                        <div class="uk-grid uk-grid-small">
                                            <?php $__currentLoopData = $_items->filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="uk-margin-small-top <?php echo e($_field['class'] ?? 'uk-width-medium'); ?>">
                                                    <?php echo $_field['data']; ?>

                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="uk-width-auto uk-padding-small-bottom">
                                        <button type="submit"
                                                name="filter"
                                                value="1"
                                                class="uk-button uk-button-primary uk-button-icon uk-border-rounded uk-margin-small-right"
                                                uk-icon="filter_list"></button>
                                        <button type="submit"
                                                name="clear"
                                                value="1"
                                                class="uk-button uk-button-danger uk-button-icon uk-border-rounded"
                                                uk-icon="cancel"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="uk-card-body">
                <?php if($_items->items->isNotEmpty()): ?>
                    <table
                        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small uk-margin-remove">
                        <thead>
                            <tr>
                                <?php $__currentLoopData = $_items->headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="<?php echo e($_td['class'] ?? NULL); ?>"
                                        style="<?php echo e($_td['style'] ?? NULL); ?>">
                                        <?php echo $_td['data'] ?? NULL; ?>

                                    </th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $_items->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="<?php echo e($_item['class'] ?? NULL); ?>"
                                    <?php echo e($_item['attributes'] ?? NULL); ?>

                                    id="<?php echo e($_item['id'] ?? NULL); ?>">
                                    <?php $__currentLoopData = ($_item['data'] ?? $_item); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_key => $_td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(is_string($_td)): ?>
                                            <td class="<?php echo e($_items->headers[$_key]['class'] ?? NULL); ?>"
                                                style="<?php echo e($_items->headers[$_key]['style'] ?? NULL); ?>">
                                                <?php echo $_td; ?>

                                            </td>
                                        <?php else: ?>
                                            <td class="<?php echo e($_items->headers[$_key]['class'] ?? NULL); ?> <?php echo e($_td['class'] ?? NULL); ?>"
                                                id="<?php echo e($_td['id'] ?? NULL); ?>"
                                                <?php echo e($_td['attributes'] ?? NULL); ?>

                                                style="<?php echo e($_items->headers[$_key]['style'] ?? NULL); ?> <?php echo e($_td['style'] ?? NULL); ?>">
                                                <?php echo $_td['data']; ?>

                                            </td>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php if($_items->pagination): ?>
                        <div class="uk-clearfix">
                            <?php echo $_items->pagination; ?>

                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        Список пуст
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if($_items->after): ?>
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-medium-bottom">
                <?php if($_items->after['header']): ?>
                    <div class="uk-card-header">
                        <h2 class="uk-text-uppercase">
                            <?php echo $_items->after['header']; ?>

                        </h2>
                    </div>
                <?php endif; ?>
                <div class="uk-card-body">
                    <?php echo $_items->after['body']; ?>

                </div>
                <?php if($_items->after['footer']): ?>
                    <div class="uk-card-footer uk-text-right">
                        <?php echo $_items->after['footer']; ?>

                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </article>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/partials/list_items.blade.php ENDPATH**/ ?>