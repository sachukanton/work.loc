{!! $_form->prefix ?? NULL !!}
<form class="uk-form use-ajax {{ $_form->class ?? NULL }}"
      id="{{ $_form->id ?? NULL }}"
      method="{{ $_form->method ?? 'GET' }}"
      enctype="multipart/form-data"
      action="{{ $_form->action ?? NULL }}">
    {{ csrf_field() }}
    {{ method_field($_form->method ?? 'GET') }}
    <div class="uk-form-alert"></div>
    <div class="uk-form-body">
        @foreach($_form->fields as $_field)
            @if($_field)
                {!! $_field !!}
            @endif
        @endforeach
    </div>
    <div class="uk-form-action uk-text-right">
        <button type="submit"
                class="uk-button {{ $_form->button['class'] ?? 'uk-button-success' }}">
            {{ $_form->button['text'] ?? 'Отправить' }}
        </button>
    </div>
</form>
{!! $_form->suffix ?? NULL !!}