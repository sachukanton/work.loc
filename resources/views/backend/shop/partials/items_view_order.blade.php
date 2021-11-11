<table id="form-field-order-products-table"
       class="uk-width-1-1 uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th class="uk-width-auto">
                Товар
            </th>
            <th class="uk-text-center uk-width-80 uk-text-danger">
                Острое
            </th>
            <th class="uk-text-right uk-width-80">
                Кол-во
            </th>
            <th class="uk-text-right uk-width-100">
                Цена, грн
            </th>
            <th class="uk-text-right uk-width-100">
                Сумма, грн
            </th>
            <th class="uk-width-50 uk-text-danger">
                <span uk-icon="icon:delete"></span>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($_item->_products as $_product)
            <tr>
                <td>
                    @if($_product->_product)
                        {!! _l(($_product->_product->model ?: $_product->_product->sku) . ":: {$_product->product_name}" . ($_product->certificate ? ' [СЕРТИФИКАТ]' : NULL), $_product->_product->generate_url, ['a' => ['target' => '_blank']]) !!}
                    @else
                        {!! "{$_product->product_sku}::{$_product->product_name}" . ($_product->certificate ? ' [СЕРТИФИКАТ]' : NULL) !!}
                    @endif
                    @if($_product->composition)
                        <div style="border-top: 1px #d2d9e5 solid;">
                            @if(isset($_product->composition['add']))
                                <div class="uk-text-bold uk-text-success">Добавить в порцию:</div>
                                @foreach($_product->composition['add'] as $_p)
                                    <div>{{ "{$_p->sku}::{$_p->title} - {$_p->weight}г ({$_p->quantity} Х {$_p->price} = {$_p->amount}грн)" }}</div>
                                @endforeach
                            @endif
                            @if(isset($_product->composition['exclude']))
                                <div class="uk-text-bold uk-text-danger">Исключить:</div>
                                @foreach($_product->composition['exclude'] as $_p)
                                    <div>{{ "{$_p->sku}::{$_p->title}" }}</div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </td>
                <td class="uk-text-center">
                    {{ is_null($_product->spicy) ? '-' : ($_product->spicy ? 'Да' : 'Нет') }}
                </td>
                <td class="uk-text-right">
                    @if($_item->status <= 2)
                        <input type="number"
                               min="0"
                               name="quantity[{{ $_product->id }}]"
                               step="1"
                               class="uk-input uk-width-60 uk-padding-small uk-height-30"
                               value="{{ $_product->quantity }}">
                    @else
                        <span class="uk-text-bold">
                            {{ $_product->quantity }}
                        </span>
                    @endif
                </td>
                <td class="uk-text-right">
                    @php
                        $_price_view = view_price($_product->price, $_product->price);
                    @endphp
                    {{ $_price_view['format']['view_price'] }}
                </td>
                <td class="uk-text-right uk-text-bold">
                    @php
                        $_amount_view = view_price($_product->amount, $_product->amount);
                    @endphp
                    {{ $_amount_view['format']['view_price'] }}
                </td>
                <td>
                    @if($_item->status <= 1)
                        <input name="remove[{{ $_product->id }}]"
                               type="checkbox"
                               class="uk-checkbox uk-margin-remove"
                               {{ $_product->status ? 'checked' : NULL }}
                               value="1">
                    @elseif($_product->status)
                        <span uk-icon="icon:done"
                              class="uk-text-danger"></span>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr class="">
            <td colspan="6">
                <div class="uk-flex uk-flex-middle uk-float-right uk-line-height-1 uk-margin-small-top{{ $_item->discount ? NULL : ' uk-text-large' }}">
                    <div>
                        @if($_item->discount)
                            Всего:
                        @else
                            Итого к оплате:
                        @endif
                    </div>
                    <div class="uk-text-bold uk-text-primary uk-margin-small-left">
                        @php
                            $_amount_view = view_price($_item->amount, $_item->amount);
                        @endphp
                        {{ $_amount_view['format']['view_price'] . ' грн' }}
                    </div>
                </div>
            </td>
        </tr>
        @if($_item->discount)
            <tr class="">
                <td colspan="6">
                    <div class="uk-flex uk-flex-middle uk-float-right uk-line-height-1 uk-margin-small-top">
                        <div>
                            Скидка:
                        </div>
                        <div class="uk-text-bold uk-text-primary uk-margin-small-left">
                            @php
                                $_amount_view = view_price($_item->discount, $_item->discount);
                            @endphp
                            {{ $_amount_view['format']['view_price'] . ' грн' }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="">
                <td colspan="6">
                    <div class="uk-flex uk-flex-middle uk-float-right uk-line-height-1 uk-margin-small-top uk-text-large">
                        <div>
                            Итого к оплате:
                        </div>
                        <div class="uk-text-bold uk-text-primary uk-margin-small-left">
                            @php
                                $_amount_view = view_price($_item->amount_less_discount, $_item->amount_less_discount);
                            @endphp
                            {{ $_amount_view['format']['view_price'] . ' грн' }}
                        </div>
                    </div>
                </td>
            </tr>
        @endif
    </tbody>
</table>
