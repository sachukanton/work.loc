<div id="box-list-last-complete-orders">
    @if($_items->isNotEmpty())
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
                        @lang('forms.fields.checkout.full_name')
                    </th>
                    <th class="uk-width-140">
                        @lang('forms.fields.checkout.phone')
                    </th>
                    <th class="uk-width-150">
                        Метод доставки
                    </th>
                    <th class="uk-width-150">
                        Метод оплаты
                    </th>
                    <th class="uk-text-right uk-width-100">
                        @lang('forms.labels.checkout.total_amount_4'), грн
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
                    @if($_authUser->hasPermissionTo('shop_orders_read'))
                        <th class="uk-text-center"
                            style="width: 55px">
                            <span uk-icon="icon: remove_red_eyevisibility"></span>
                        </th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @foreach($_items as $_item)
                    @php
                        $_amount = $_item->amount_less_discount ?: $_item->amount;
                        $_amount = view_price($_amount, $_amount);
                    @endphp
                    <tr class="order-status-{{ $_item->status }}">
                        <td class="uk-text-black">
                            {{ "#{$_item->id}" }}
                        </td>
                        <td class="uk-text-black">
                            {{ "#{$_item->rk_order_number}" }}
                        </td>
                        <td>
                            {{ $_item->type == 'full' ? 'Полный' : 'Быстрый' }}
                        </td>
                        <td>
                            {{ $_item->user_full_name }}
                        </td>
                        <td>
                            {!! $_item->format_phone !!}
                        </td>
                        <td>
                            {{ $_item->delivery_method ? trans('shop.delivery_method.delivery_method_' .$_item->delivery_method) : '-//-' }}
                        </td>
                        <td>
                            {{ $_item->payment_method ? trans('shop.payment_method.payment_method_' .$_item->payment_method) : '-//-' }}
                        </td>
                        <td class="uk-text-right uk-text-black uk-text-primary"
                            id="{{ "order-{$_item->id}-data" }}">
                            {{ $_amount['format']['view_price'] }}
                        </td>
                        <td class="uk-text-center">
                            {{ $_item->pre_order_at ? $_item->pre_order_at->format('d.m.Y - H:i') : 'Нет' }}
                        </td>
                        <td class="uk-text-center">
                            {{ $_item->created_at->format('d.m.Y - H:i') }}
                        </td>
                        <td class="uk-text-center">
                            @lang('shop.status.' . $_item->status)
                        </td>
                        @if($_authUser->hasPermissionTo('shop_orders_read'))
                            <td class="uk-text-center">
                                <a href="{{ _r('oleus.shop_orders.edit', $_item) }}"
                                   class="uk-button uk-button-success uk-button-icon uk-border-rounded uk-button-small">
                                    <span uk-icon="icon: remove_red_eyevisibility"></span>
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove-bottom uk-margin-top">
            @lang('frontend.no_items')
        </div>
    @endif
</div>
