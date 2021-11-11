@extends('mail.mail')

@section('body')
    @include('mail.header')
    <a href="{{ $_verification_email_url }}"
       target="_blank">
        @lang('mail.verification_link_email')
    </a>
    @include('mail.footer')
@endsection