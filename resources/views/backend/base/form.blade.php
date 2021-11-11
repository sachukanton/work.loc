@if($_item->prefix)
    {!! $_item->prefix !!}
@endif
<form method="post"
      enctype="multipart/form-data"
      id="{{ $_form_data->form_id }}"
      action="{{ _r('ajax.submit_form', ['form' => $_item->id]) }}"
      class="use-ajax uk-form uk-padding-small uk-position-relative{{ $_item->style_class ? " {$_item->style_class}" : NULL }}">
    {!! csrf_field() !!}
    {!! method_field('POST') !!}
    <input type="hidden"
           name="form_index"
           value="{{ $_item->render_index }}">
    @if($_item->hidden_title == 0)
        <h2 class="uk-text-bold uk-heading-divider uk-margin-remove-top">
            {!! $_item->title !!}
        </h2>
        @if($_item->sub_title)
            <div class="uk-text-meta uk-margin-bottom uk-text-center">
                {!! $_item->sub_title !!}
            </div>
        @endif
    @endif
    @if($_form_data->use_steps)
        @include('backend.base.form_steps_item', ['_step_item' => $_form_data->steps[$_form_data->first_step], '_form' => $_item, '_form_data' => $_form_data])
    @else
        @foreach($_form_data->render_fields as $_field)
            {!! $_field !!}
        @endforeach
        <div class="uk-margin uk-form-action uk-clearfix">
            <button type="submit"
                    class="uk-button uk-button-success uk-float-right {{ $_item->settings->send->class ?? NULL }}"
                    value="1"
                    name="send_form">
                {{ $_item->button_send ?: 'Send the Form' }}
            </button>
        </div>
    @endif
    @if($_item->body)
        <div class="uk-margin uk-text-small uk-text-muted">
            {!! $_item->body !!}
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
@if($_item->suffix)
    {!! $_item->suffix !!}
@endif