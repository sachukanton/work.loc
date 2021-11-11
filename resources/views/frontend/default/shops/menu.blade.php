<div id="uk-items-list-menu">
    @if($_item->filterPage instanceof  \App\Models\Shop\FilterPage && ($_menu = $_item->filterPage->menu))
        <div class="items-list-menu">
            <div class="title uk-text-uppercase">
                @lang('frontend.also_filter_page')
            </div>
            <ul uk-nav>
                @foreach($_menu as $_li)
                    <li>
                        @l($_li['title'], $_li['alias'])
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>