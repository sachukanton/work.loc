@if($_form->modal)
    <div class="uk-modal-body">
        <button class="uk-modal-close-outside"
                type="button"
                uk-close></button>
        <div class="shortcut-form modal uk-padding-remove">
            @endif
            @if($_form->prefix)
                {!! $_form->prefix !!}
            @endif
            <form method="post"
                  enctype="multipart/form-data"
                  id="{{ $_form->id }}"
                  action="{{ $_form->action }}"
                  class="uk-form {{ $_form->form_class ? " {$_form->form_class}" : NULL }}{{ $_form->ajax ? ' use-ajax' : NULL }}">
                <input type="hidden"
                       name="form"
                       value="{{ $_form->id }}">
                @if($_form->title)
                    <h2 class="title-02 uk-position-relative uk-position-z-index uk-margin-remove-top">
                        {!! $_form->title !!}
                    </h2>
                @endif
                @foreach($_form->fields as $_field)
                    {!! $_field !!}
                @endforeach
                @if($_form->body)
                    <div class="uk-margin uk-text-small uk-text-muted">
                        {!! $_form->body !!}
                    </div>
                @endif
                <div class="uk-form-action uk-clearfix">
                    @if($_form->buttons)
                        @foreach($_form->buttons as $_button)
                            {!! $_button !!}
                        @endforeach
                    @else
                        <button type="submit"
                                class="uk-button btn-submit uk-position-relative uk-flex-middle uk-margin-auto {{ $_form->button_send_class }}"
                                value="1"
                                name="send_form">
                            {{ $_form->button_send_title ?: trans('Send the Form') }}
                        </button>
                    @endif
                </div>
                {!! csrf_field() !!}
                {!! method_field('POST') !!}
                <input type="hidden"
                       name="form_id"
                       value="{{ $_form->id }}">
            </form>
            @if($_form->suffix)
                {!! $_form->suffix !!}
            @endif
            @if($_form->modal)
        </div>
    </div>
    </div>
@endif