<div id="box-list-last-complete-orders">
    <?php if($_items->isNotEmpty()): ?>
        <table
            class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small uk-margin-remove-bottom">
            <thead>
                <tr>
                    <th class="uk-width-xsmall uk-text-center">
                        ID
                    </th>
                    <th class="uk-width-130">
                        IIKO ID
                    </th>
                    <th class="uk-width-150">
                        Тип заказа
                    </th>
                    <th class="">
                        <?php echo app('translator')->getFromJson('forms.fields.checkout.full_name'); ?>
                    </th>
                    <th class="uk-width-140">
                        <?php echo app('translator')->getFromJson('forms.fields.checkout.phone'); ?>
                    </th>
                    <th class="uk-width-150">
                        Метод доставки
                    </th>
                    <th class="uk-width-150">
                        Метод оплаты
                    </th>
                    <th class="uk-text-right uk-width-100">
                        <?php echo app('translator')->getFromJson('forms.labels.checkout.total_amount_4'); ?>, грн
                    </th>
                    <th class="uk-text-right uk-width-130">
                        <span uk-icon="icon: timer"></span> Предзаказ
                    </th>
                    <th class="uk-text-center uk-width-130">
                        <span uk-icon="icon: timer"></span> Создания
                    </th>
                    <th class="uk-text-center uk-width-130">
                        Статус
                    </th>
                    <?php if($_authUser->hasPermissionTo('shop_orders_read')): ?>
                        <th class="uk-text-center"
                            style="width: 55px">
                            <span uk-icon="icon: remove_red_eyevisibility"></span>
                        </th>
                    <?php endif; ?>

                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $_amount = $_item->amount_less_discount ?: $_item->amount;
                        $_amount = view_price($_amount, $_amount);
                    ?>
                    <tr class="order-status-<?php echo e($_item->status); ?>">
                        <td class="uk-text-black">
                            <?php echo e("#{$_item->id}"); ?>

                        </td>
                        <td class="uk-text-black">
                            <?php echo e("#{$_item->rk_order_number}"); ?>

                        </td>
                        <td>
                            <?php echo e($_item->type == 'full' ? 'Полный' : 'Быстрый'); ?>

                        </td>
                        <td>
                            <?php echo e($_item->user_full_name); ?>

                        </td>
                        <td>
                            <?php echo $_item->format_phone; ?>

                        </td>
                        <td>
                            <?php echo e($_item->delivery_method ? trans('shop.delivery_method.delivery_method_' .$_item->delivery_method) : '-//-'); ?>

                        </td>
                        <td>
                            <?php echo e($_item->payment_method ? trans('shop.payment_method.payment_method_' .$_item->payment_method) : '-//-'); ?>

                        </td>
                        <td class="uk-text-right uk-text-black uk-text-primary"
                            id="<?php echo e("order-{$_item->id}-data"); ?>">
                            <?php echo e($_amount['format']['view_price']); ?>

                        </td>
                        <td class="uk-text-center">
                            <?php echo e($_item->pre_order_at ? $_item->pre_order_at->format('d.m.Y - H:i') : 'Нет'); ?>

                        </td>
                        <td class="uk-text-center">
                            <?php echo e($_item->created_at->format('d.m.Y - H:i')); ?>

                        </td>
                        <td class="uk-text-center">
                            <?php echo app('translator')->getFromJson('shop.status.' . $_item->status); ?>
                        </td>
                        <?php if($_authUser->hasPermissionTo('shop_orders_read')): ?>
                            <td class="uk-text-center">
                                <a href="<?php echo e(_r('oleus.shop_orders.edit', $_item)); ?>"
                                   class="uk-button uk-button-success uk-button-icon uk-border-rounded uk-button-small">
                                    <span uk-icon="icon: remove_red_eyevisibility"></span>
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove-bottom uk-margin-top">
            <?php echo app('translator')->getFromJson('frontend.no_items'); ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/test09/data/www/test09.ukrmisto.com/resources/views/backend/shop/partials/items_last_complete_orders.blade.php ENDPATH**/ ?>