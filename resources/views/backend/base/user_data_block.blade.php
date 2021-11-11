@php
    $_compareCount = ShopCompare::getCount();
@endphp
@if($_authUser)
    @php
        $_profileUser = $_authUser->_profile;
    @endphp
    <div class="uk-flex uk-flex-middle">
        {!! image_render($_profileUser->_avatar, 'account_avatar_small', ['attributes' => ['class' => 'uk-border-circle']]) !!}
        @l($_authUser->full_name, 'personal_area', ['attributes' => ['class' => 'uk-display-block uk-margin-small-left uk-text-color-white']])
        @l('<span uk-icon="icon: compare_arrows"></span><span class="uk-badge uk-badge-success uk-position-absolute uk-position-top-right ' . ($_compareCount ? NULL : 'uk-hidden ') . 'uk-animation-scale-up uk-animation-fast shop-compare-products-badge" style="margin:-6px -6px 0 0;">'. $_compareCount .'</span>', 'page.shop_compare_products', ['attributes' => ['class' => 'uk-flex uk-flex-middle uk-flex-center uk-margin-small-left uk-position-relative uk-text-color-white', 'style' => 'height:40px;width:40px;']])
    </div>
@else

@endif