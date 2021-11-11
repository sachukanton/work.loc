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
                        {{ trans('mail.data_' . $_item->type ) }}
                    </strong>
                </td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.comment_node'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    <a href="{{ $_site_url . $_item->model->generate_url }}"
                       target="_blank">
                        {{ $_item->model->title }}
                    </a>
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.comment_rate'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item::RATE_STAR_LABEL[$_item->rate] ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.comment_advantages'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->comment_advantages ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
            <tr>
                <td width="5%"></td>
                <td align="right"
                    valign="top"
                    width="30%">
                    <strong>
                        @lang('mail.comment_disadvantages'):
                    </strong>
                </td>
                <td align="left"
                    valign="middle"
                    width="60%">
                    {!! $_item->comment_disadvantages ?: '-//-' !!}
                </td>
                <td width="5%"></td>
            </tr>
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
        </tbody>
    </table>
    @include('mail.footer')
@endsection