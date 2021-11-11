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
                    {!! $_item->_form->title !!}
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
        </tbody>
    </table>
    @include('mail.footer')
@endsection