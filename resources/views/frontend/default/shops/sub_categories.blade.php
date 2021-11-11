@if($_sub_categories)
    <div id="uk-items-list-sub-categories" class="menu-sub-catalog">
        <ul uk-grid class="uk-nav uk-grid-small uk-grid">
            @foreach($_sub_categories as $_sub_category)
                <li class="uk-width-1-5@l uk-width-1-3@s">
                    @l(str_limit(strip_tags($_sub_category['title']), 40), $_sub_category['alias'], ['attributes' => ['title' => strip_tags($_sub_category['title']), 'class' => 'level-item-1 uk-text-uppercase']])
                </li>
            @endforeach
            </ul>
     </div>
@endif