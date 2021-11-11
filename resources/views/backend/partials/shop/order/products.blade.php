<table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small uk-margin-remove">
    <thead>
        <tr>
            <th class="uk-width-small">SKU</th>
            <th>Товар</th>
            {{--            <th width="80"--}}
            {{--                class="uk-text-danger uk-text-center">Острое--}}
            {{--            </th>--}}
            <th width="80">Кол-во, ед.</th>
            <th width="80"
                class="uk-text-right">Цена за ед.
            </th>
            <th width="90"
                class="uk-text-right">Сумма
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($_items as $_item)
            <tr>
                <td>
                    {{ $_item->sku }}
                </td>
                <td>
                    @if($_item->url)
                        <a href="{{ $_item->url }}"
                           target="_blank">
                            {{ $_item->product_name . ($_item->certificate ? ' [СЕРТИФИКАТ]' : NULL) }}
                        </a>
                    @else
                        {{ $_item->product_name }}
                    @endif
                    @if($_item->composition)
                        <div style="border-top: 1px #d2d9e5 solid;">
                            @if(isset($_item->composition['add']))
                                <div class="uk-text-bold uk-text-success">Добавить в порцию:</div>
                                @foreach($_item->composition['add'] as $_p)
                                    <div>{{ "{$_p->sku}::{$_p->title} - {$_p->weight}г ({$_p->quantity} Х {$_p->price} = {$_p->amount}грн)" }}</div>
                                @endforeach
                            @endif
                            @if(isset($_item->composition['exclude']))
                                <div class="uk-text-bold uk-text-danger">Исключить:</div>
                                @foreach($_item->composition['exclude'] as $_p)
                                    <div>{{ "{$_p->sku}::{$_p->title}" }}</div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </td>
                {{--                <td class="uk-text-center">--}}
                {{--                    {{ is_null($_item->spicy) ? '-' : ($_item->spicy ? 'Да' : 'Нет') }}--}}
                {{--                </td>--}}
                <td>
                    {{ $_item->quantity }}
                </td>
                <td class="uk-text-right">
                    {!! "{$_item->price_view['format']['view_price']} {$_item->price_view['currency']['suffix']}" !!}
                </td>
                <td class="uk-text-right">
                    {!! "{$_item->amount_view['format']['view_price']} {$_item->amount_view['currency']['suffix']}" !!}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="{{ $_entity->discount ? 'uk-h4' : 'uk-h3' }}">
            <td colspan="4"
                class="uk-text-right">
                @if($_entity->discount)
                    Всего:
                @else
                    Итого:
                @endif
            </td>
            <td class="uk-text-right uk-text-primary"
                colspan="2">
                {!! "{$_entity->amount_view['format']['view_price']} {$_entity->amount_view['currency']['suffix']}" !!}
            </td>
        </tr>
        @if($_entity->discount)
            <tr class="uk-h4">
                <td colspan="4"
                    class="uk-text-right">
                    Скидка:
                </td>
                <td class="uk-text-right uk-text-primary"
                    colspan="2">
                    {!! view_price($_entity->discount, $_entity->discount)['format']['view_price'] . ' ' . $_entity->amount_view['currency']['suffix'] !!}
                </td>
            </tr>
            <tr class="uk-h3">
                <td colspan="4"
                    class="uk-text-right">
                    Итого:
                </td>
                <td class="uk-text-right uk-text-primary"
                    colspan="2">
                    {!! view_price($_entity->amount_less_discount, $_entity->amount_less_discount)['format']['view_price'] . ' ' . $_entity->amount_view['currency']['suffix'] !!}
                </td>
            </tr>
        @endif
    </tfoot>
</table>
{{--<div class="uk-margin-top uk-text-right">--}}
{{--    @l('Скачать файл', 'oleus.shop_orders.download', ['p' => ['shop_order' => $_entity], 'attributes' => ['class' =>--}}
{{--    'uk-button uk-button-color-amber']])--}}
{{--</div>--}}
