@if($_item->prefix)
    {!! $_item->prefix !!}
@endif
<div class="shortcut-form uk-position-relative uk-margin-auto">
    <img class="logo-form" uk-img="data-src:{{ formalize_path('template/images/logo-form.svg') }}"
         alt="">
    <form method="post"
          enctype="multipart/form-data"
          id="{{ $_form_data->form_id }}"
          action="{{ _r('ajax.submit_form', ['form' => $_item->id]) }}"
          class="use-ajax uk-form uk-position-relative {{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
        {!! csrf_field() !!}
        {!! method_field('POST') !!}
        <input type="hidden"
               name="form_index"
               value="{{ $_item->render_index }}">
        @if($_item->hidden_title == 0)
            <h2 class="uk-text-uppercase">{!! $_item->title !!}</h2>
            @if($_item->sub_title)
                <div class="uk-text-meta uk-margin-bottom uk-text-center">
                    {!! $_item->sub_title !!}
                </div>
            @endif
        @endif
        @if($_item->body)
            <div class="form-description uk-text-right">
                {!! $_item->body !!}
            </div>
        @endif
        @if($_form_data->use_steps)
            @include('backend.base.form_steps_item', ['_step_item' => $_form_data->steps[$_form_data->first_step], '_form' => $_item, '_form_data' => $_form_data])
        @else
            <div class="uk-grid-small uk-grid">
                @foreach($_form_data->render_fields as $_field)
                    {!! $_field !!}
                @endforeach
            </div>
            <div class="uk-form-action uk-clearfix uk-text-center uk-margin">
                <button type="submit"
                        class="uk-button uk-button-buy uk-text-uppercase uk-position-relative {{ $_item->settings->send->class ?? NULL }}"
                        value="1"
                        name="send_form">
                    {{ $_item->button_send ?: 'Send the Form' }}
                </button>
            </div>
        @endif
        @if(isset($_accessEdit['form']) && $_accessEdit['form'])
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                @if($_locale == DEFAULT_LOCALE)
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.forms.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @else
                    @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.forms.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
                @endif
            </div>
        @endif
    </form>
</div>
@if($_item->suffix)
    {!! $_item->suffix !!}
@endif