@extends('mail.mail')

@section('body')
    @include('mail.header')
    <table width="100%"
           cellspacing="15"
           cellpadding="0"
           border="0"
           style="color:#3a3c4c;">
        <tbody>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        Тип оформления:
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->type == 'quick' ? 'Быстрая форма' : 'Полная форма' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        ID заказа:
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->id !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        Статус заказа:
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%"
                    style="font-weight: 700; {{ $_item->status == 0 || $_item->status == -1 ? 'color: #f00;' : NULL }}">
                    {!! trans($_item::ORDER_STATUS[$_item->status]) !!} {{ $_item->status == 0 || $_item->status == -1 ? '(Требуется проверка)' : NULL }}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        Номер заказа в IIKO:
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->rk_order_number !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.send_date'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->created_at->format('d-m-Y') !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.send_time'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->created_at->format('H:i') !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.payment_method'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {{ $_item->payment_method ? trans("shop.payment_methods.payment_method_{$_item->payment_method}") : '-//-' }}
                </td>
                <td width="5%"></td>
            </tr>
            @if($_item->payment_method == 'cash')
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.payment_surrender'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->surrender }}
                    </td>
                    <td width="2%"></td>
                </tr>
            @endif
            @if($_item->payment_method == 'card')
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.payment_transaction_number'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->payment_transaction_number }}
                    </td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.payment_transaction_status'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->payment_transaction_status && isset($_item::LIQPAY_STATUS[$_item->payment_transaction_status]) ? $_item::LIQPAY_STATUS[$_item->payment_transaction_status] : null }}
                    </td>
                    <td width="2%"></td>
                </tr>
            @endif
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.delivery_method'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {{ $_item->delivery_method ? trans("shop.delivery_method.delivery_method_{$_item->delivery_method}") : '-//-' }}
                </td>
                <td width="5%"></td>
            </tr>
            @if($_item->delivery_method == 'delivery')
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.delivery_address'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->formation_address }}
                    </td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.delivery_free'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->delivery_free ? 'Да' : 'Нет' }}
                    </td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="2%"></td>
                    <td align="right"
                        valign="top"
                        width="36%">
                        <strong>
                            @lang('mail.delivery_pre_order'):
                        </strong>
                    </td>
                    <td align="left"
                        valign="middle"
                        colspan="2"
                        width="60%">
                        {{ $_item->pre_order_at ? $_item->pre_order_at->format('Y-m-d H:i:s') : 'Нет' }}
                    </td>
                    <td width="2%"></td>
                </tr>
            @endif
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.comment'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->comment ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td colspan="4"
                    align="center">
                    <strong style="color:#308862;text-transform:uppercase;font-weight:400;">
                        @lang('mail.personal_info')
                    </strong>
                </td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.user_surname'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->surname ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.user_name'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->name ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.user_patronymic'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->patronymic ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.user_phone'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->phone ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.user_email'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->email ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td colspan="4"
                    align="center">
                    <strong style="color:#308862;text-transform:uppercase;font-weight:400;">
                        @lang('mail.order_products')
                    </strong>
                </td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td width="90%"
                    colspan="2">
                    <table border="0"
                           width="100%"
                           cellpadding="5"
                           cellspacing="0"
                           style="border-top:1px #cccccc solid;border-left:1px #cccccc solid;">
                        <thead>
                            <tr>
                                <th style="border-right:1px #cccccc solid;border-bottom:2px #cccccc solid;">
                                    @lang('mail.product_name')
                                </th>
                                <th style="border-right:1px #cccccc solid;border-bottom:2px #cccccc solid;">
                                    @lang('mail.product_quantity')
                                </th>
                                <th style="border-right:1px #cccccc solid;border-bottom:2px #cccccc solid;">
                                    @lang('mail.product_price')
                                </th>
                                <th style="border-right:1px #cccccc solid;border-bottom:2px #cccccc solid;">
                                    @lang('mail.product_amount')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($_item->products as $_product)
                                <tr>
                                    <td style="border-left:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                        {{ $_product->product_name }}
                                    </td>
                                    <td style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;"
                                        width="100"
                                        align="center">
                                        {{ $_product->quantity }}
                                    </td>
                                    <td style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;"
                                        width="170"
                                        align="right">
                                        {!! $_product->price ? "{$_product->price_view['format']['view_price']} <i>{$_product->price_view['currency']['suffix']}</i>" : '-//-' !!}
                                    </td>
                                    <td style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;"
                                        width="150"
                                        align="right">
                                        {!! $_product->amount ? "{$_product->amount_view['format']['view_price']} <i>{$_product->amount_view['currency']['suffix']}</i>" : '-//-' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"
                                    align="right"
                                    style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                    @lang('mail.order_amount')
                                </td>
                                <td align="right"
                                    style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                    <strong>
                                        {!! $_item->amount ? "{$_item->amount_view['format']['view_price']} <i>{$_item->amount_view['currency']['suffix']}</i>" : '-//-' !!}
                                    </strong>
                                </td>
                            </tr>
                            @if($_item->discount)
                                <tr>
                                    <td colspan="3"
                                        align="right"
                                        style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                        @lang('mail.discount_amount')
                                    </td>
                                    <td align="right"
                                        style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                        <strong>
                                            {!! $_item->discount ? "{$_item->discount_view['format']['view_price']} <i>{$_item->discount_view['currency']['suffix']}</i>" : '-//-' !!}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"
                                        align="right"
                                        style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                        @lang('mail.total_amount')
                                    </td>
                                    <td align="right"
                                        style="border-right:1px #cccccc solid;border-bottom:1px #cccccc solid;">
                                        <strong>
                                            {!! $_item->amount_less_discount ? "{$_item->amount_less_discount_view['format']['view_price']} <i>{$_item->amount_less_discount_view['currency']['suffix']}</i>" : '-//-' !!}
                                        </strong>
                                    </td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </td>
                <td width="5%"></td>
            </tr>
        </tbody>
    </table>
    @include('mail.footer')
@endsection
