</td>
</tr>
<tr>
    <td></td>
</tr>
<tr>
    <td>
        @if($_site_contacts['phones'])
            @foreach($_site_contacts['phones'] as $_phone)
                <span style="margin-right: 10px; color: #000; text-decoration: none;">
                    {!! $_phone['original'] !!}
                </span>
            @endforeach
        @endif
        @if($_site_contacts['email'])
            <a style="margin-right: 15px; color: #000; text-decoration: none;"
               href="mailto:{{ $_site_contacts['email'] }}"
               title="{{ $_site_contacts['email'] }}">
                {!! $_site_contacts['email'] !!}
            </a>
        @endif
    </td>
</tr>
<tr>
    <td>
                <span style="font-size: .8em; color: #aaaaaa;">
                    {{ str_replace(':year', date('Y'), $_site_data['site_copyright']) }}
                </span>
    </td>
</tr>
</table>
