<div id="{{ $_form_data->form_id }}-steps-box">
    @if($_step_item['title'])
        <h3 class="uk-text-bold uk-heading-bullet uk-margin-remove-top">
            {!! $_step_item['title'] !!}
        </h3>
    @endif
    @foreach($_step_item['fields'] as $_field)
        {!! $_field !!}
    @endforeach
    <div class="uk-margin uk-form-action uk-clearfix">
        @if ($_step_item['prev'])
            <button type="submit"
                    class="uk-button uk-button-default uk-float-left {{ $_form->settings->send->class ?? NULL }}"
                    name="previous_step_form"
                    value="{{ $_step_item['prev'] }}">
                Previous Step
            </button>
        @endif
        @if ($_step_item['next'])
            <button type="submit"
                    class="uk-button uk-button-default uk-float-right {{ $_form->settings->send->class ?? NULL }}"
                    name="next_step_form"
                    value="{{ $_step_item['next'] }}">
                Next Step
            </button>
        @endif
        @if($_form_data->last_step == $_step_item['id'])
            <button type="submit"
                    class="uk-button uk-button-default uk-float-right {{ $_form->settings->send->class ?? NULL }}"
                    name="send_form"
                    value="1">
                {{ $_form->button_send ?: 'Send the Form' }}
            </button>
        @endif
    </div>
</div>