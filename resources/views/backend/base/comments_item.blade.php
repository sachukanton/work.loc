<article class="uk-comment uk-visible-toggle uk-card uk-card-body uk-border-rounded uk-card-small uk-box-shadow-small lighten-5 uk-position-relative{{ !$_comment->status ? ' uk-background-color-red' : ' uk-background-color-grey' }}"
         id="comment-item-{{ $_comment->id }}">
    <header class="uk-comment-header uk-position-relative">
        <div class="uk-grid-medium uk-flex-middle"
             uk-grid>
            <div class="uk-width-auto">
                @if($_comment->_user->exists)
                    {!! image_render($_comment->_user->_profile->_avatar, 'account_avatar', ['attributes' => ['class' => 'uk-border-circle', 'width' => 60, 'height' => 60]]) !!}
                @else
                    <img src="{{ formalize_path('images/no-user-avatar.svg') }}"
                         width="60"
                         height="60"
                         class="uk-border-circle">
                @endif
            </div>
            <div class="uk-width-expand">
                <div class="uk-comment-title uk-margin-remove uk-h4">
                    @if(!isset($isReply))
                        {{ $_comment->name ? $_comment->name : trans('frontend.anonymous_user') }}
                    @else
                        {{ $_comment->name ?: $_comment->_user->full_name }}
                    @endif
                </div>
                <div class="uk-comment-meta uk-margin-remove-top">
                    {{ $_comment->published_date }}
                </div>
                @if(!isset($isReply))
                    <div class="uk-display-inline-block">
                        @for($i = 1; $i < 6; $i++)
                            <span uk-icon="icon:star"
                                  class="{{ $_comment->rate >= $i ? 'uk-text-color-amber darken-2' : 'uk-text-color-grey lighten-3' }}"></span>
                        @endfor
                    </div>
                @endif
            </div>
        </div>
        @if($_authUser && $_authUser->can('comments_reply'))
            <div class="uk-position-top-right uk-position-small">
                <button type="button"
                        class="uk-button uk-button-success uk-button-small use-ajax"
                        data-path="{{ _r('ajax.open_comments_form', ['comment' => $_comment->id]) }}"
                        data-entity="{{ $_item->_alias->id }}">
                    @lang("forms.buttons.comment.reply_{$_comment->type}")
                </button>
            </div>
        @endif
    </header>
    <div class="uk-comment-body">
        <p>
            {{ strip_tags($_comment->comment) }}
        </p>
        @if(!isset($isReply))
            <div uk-grid
                 class="uk-child-width-1-2 uk-grid-divider">
                <div>
                    <div class="uk-text-bold uk-text-color-green">
                        @lang('forms.labels.comment.advantages')
                    </div>
                    <p>
                        {{ $_comment->advantages ?: trans('forms.values.value_not') }}
                    </p>
                </div>
                <div>
                    <div class="uk-text-bold uk-text-color-red">
                        @lang('forms.labels.comment.disadvantages')
                    </div>
                    <p>
                        {{ $_comment->advantages ?: trans('forms.values.value_not') }}
                    </p>
                </div>
            </div>
        @endif
    </div>
    @if(isset($_accessEdit['comment']) && $_accessEdit['comment'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.comments.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
        </div>
    @endif
</article>
@if($_comment->_reply->isNotEmpty())
    <ul>
        @foreach($_comment->_reply as $_reply)
            <li>
                @include('backend.base.comments_item', ['_comment' => $_reply, 'isReply' => TRUE])
            </li>
        @endforeach
    </ul>
@endif


