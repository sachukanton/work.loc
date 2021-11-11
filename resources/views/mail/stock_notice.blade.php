@extends('mail.mail')

@section('body')
    @include('mail.header')
    <div>
        Товар "{!! $_item->link_to_product !!}" повиялся в наличии.
    </div>
    @include('mail.footer')
@endsection