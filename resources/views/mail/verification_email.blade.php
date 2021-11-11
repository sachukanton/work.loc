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
                <td align="center"
                    valign="middle"
                    width="90%">
                    <a href="{{ $_verification_email_url }}"
                       style="color:#3a3c4c;"
                       target="_blank">
                        @lang('mail.verification_link_email')
                    </a>
                </td>
                <td width="5%"></td>
            </tr>
        </tbody>
    </table>
    @include('mail.footer')
@endsection