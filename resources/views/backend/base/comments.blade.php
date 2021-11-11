@php
    $_comments = $_item->getComments();
    $_have_comments = $_comments->isNotEmpty();
@endphp
<div class="uk-position-relative uk-margin-medium-top uk-margin-medium-bottom"
     id="comments-block">
    <div class="uk-h2 uk-text-bold uk-heading-divider uk-margin-remove-top">
        @if($_have_comments)
            @lang('frontend.block.comments_title')
        @else
            @lang('frontend.block.comments_title_new_comment')
        @endif
    </div>
    <div class="uk-position-relative">
        @if($_have_comments)
            <div uk-grid>
                <div class="uk-width-3-4">
                    @include('backend.base.comments_items', ['_rate' => 'all'])
                </div>
                <div class="uk-width-1-4">
                    <button type="button"
                            class="uk-button uk-button-success use-ajax"
                            data-path="{{ _r('ajax.open_comments_form') }}"
                            data-view="full"
                            data-type="review"
                            data-entity="{{ $_item->_alias->id }}">
                        @lang('frontend.button.new_comment')
                    </button>
                </div>
            </div>
        @else
            @include('backend.base.comments_form', [
                '_entity' => $_item->_alias,
                '_isFull' => TRUE,
                '_isFirst' => TRUE,
            ])
        @endif
    </div>
</div>