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
                        @lang('mail.form'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_form !!}
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
                        @lang('mail.product_name'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_product !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.product_quantity'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->quantity !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.product_price'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->_product->_render_price()['price']['format']['view_price'] ?? '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            {{--            <tr>--}}
            {{--                <td width="5%"></td>--}}
            {{--                <td align="right"--}}
            {{--                    valign="top"--}}
            {{--                    width="30%">--}}
            {{--                    <strong>--}}
            {{--                        @lang('mail.comment'):--}}
            {{--                    </strong>--}}
            {{--                </td>--}}
            {{--                <td align="left"--}}
            {{--                    valign="middle"--}}
            {{--                    width="60%">--}}
            {{--                    {!! $_item->comment ?: '-//-' !!}--}}
            {{--                </td>--}}
            {{--                <td width="5%"></td>--}}
            {{--            </tr>--}}
            @if($_item->data)
                @foreach($_item->data as $_field)
                    <tr>
                        <td width="5%"></td>
                        <td align="right"
                            valign="top"
                            width="30%">
                            <strong>
                                {{ $_field->label }}:
                            </strong>
                        </td>
                        <td align="left"
                            valign="middle"
                            width="60%">
                            {!! $_field->data ?: '-//-' !!}
                        </td>
                        <td width="5%"></td>
                    </tr>
                @endforeach
            @endif
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
            {{--            <tr>--}}
            {{--                <td width="5%"></td>--}}
            {{--                <td align="right"--}}
            {{--                    valign="top"--}}
            {{--                    width="30%">--}}
            {{--                    <strong>--}}
            {{--                        @lang('mail.user_email'):--}}
            {{--                    </strong>--}}
            {{--                </td>--}}
            {{--                <td align="left"--}}
            {{--                    valign="middle"--}}
            {{--                    width="60%">--}}
            {{--                    {!! $_item->email ?: '-//-' !!}--}}
            {{--                </td>--}}
            {{--                <td width="5%"></td>--}}
            {{--            </tr>--}}
        </tbody>
    </table>
    @include('mail.footer')
@endsection
