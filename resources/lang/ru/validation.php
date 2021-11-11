<?php

return [
    'accepted'             => 'Поле ":attribute" должно быть выбрано.',
    'active_url'           => 'Значение поля ":attribute" имеет не правильный формат URL.',
    'after'                => 'Значение поля ":attribute" должно быть датой поздней  :date.',
    'after_or_equal'       => 'Значение поля ":attribute" должно быть датой поздней или равной :date.',
    'alpha'                => 'Поле ":attribute" может содержать только буквы.',
    'alpha_dash'           => 'Поле ":attribute" может содержать только буквы, цифры, тире или подчеркивание.',
    'alpha_num'            => 'Поле ":attribute" может содержать только буквы и цифры.',
    'array'                => 'Значение поля ":attribute" должно содержать массив.',
    'before'               => 'Значение поля ":attribute" должно быть датой предшевствующей :date.',
    'before_or_equal'      => 'Значение поля ":attribute" должно быть датой предшевствующей или равной :date.',
    'between'              => [
        'numeric' => 'Значение поля ":attribute" должно быть в промежутке меджу от :min до :max.',
        'file'    => 'Файл ":attribute" должен быть в промежутке от :min до :max КБ.',
        'string'  => 'Значение поля ":attribute" должно быть в промежутке меджу символами от :min до :max.',
        'array'   => 'Значение поля ":attribute" должно содержать от :min до :max элементов.',
    ],
    'boolean'              => 'Поле ":attribute" доблжно быть булевым.',
    'confirmed'            => 'Подтверждение поля ":attribute" не совпадает.',
    'date'                 => 'Значение поля ":attribute" не является датой.',
    'date_equals'          => 'Значение поля ":attribute" должно быть датой равно :date.',
    'date_format'          => 'Значение поля ":attribute" не соответствует формату :format.',
    'different'            => 'Значение полей ":attribute" и ":other" должны отличаться.',
    'digits'               => 'Поле ":attribute" должно быть числовым и иметь длину, равную :digits.',
    'digits_between'       => 'Поле ":attribute" должно иметь длину в диапазоне между :min и :max.',
    'dimensions'           => 'Файл ":attribute" должен быть изображением с подходящими под ограничения размерами.',
    'distinct'             => 'Поле ":attribute" не должно содержать дублирующих значений.',
    'email'                => 'Значение поля ":attribute" должно быть адресом e-mail.',
    'ends_with'            => 'Поле ":attribute" должно заканчиваться отдним из значений: :values',
    'exists'               => 'Поле ":attribute" должно существовать в заданной таблице БД.',
    'file'                 => 'Поле ":attribute" должно быть успешно загруженным файлом.',
    'filled'               => 'Поле ":attribute" не должно быть пустым, если оно есть.',
    'gt'                   => [
        'numeric' => 'Значение поля ":attribute" должно быть больше чем :value.',
        'file'    => 'Файл ":attribute" должен быть больше чем :value КБ.',
        'string'  => 'Значение поля ":attribute" должно быть больше чем :value символ.',
        'array'   => 'Значение поля ":attribute" должно содержать больше чем :value элементов.',
    ],
    'gte'                  => [
        'numeric' => 'Значение поля ":attribute" должно быть больше или равно :value.',
        'file'    => 'Файл ":attribute" должен быть больше или равен :value КБ.',
        'string'  => 'Значение поля ":attribute" должно быть больше или равно :value символ.',
        'array'   => 'Значение поля ":attribute" должно содержать больше или равно :value элкментов.',
    ],
    'image'                => 'Загруженный файл ":attribute" должен быть изображением в формате JPEG, PNG, BMP, GIF или SVG.',
    'in'                   => 'Значение поля ":attribute" не допустимо.',
    'in_array'             => 'Значение в поле ":attribute" должно быть одним из значений поля ":other".',
    'integer'              => 'Поле ":attribute" должно иметь корректное целочисленное значение.',
    'ip'                   => 'Поле ":attribute" должно быть корректным IP-адресом.',
    'ipv4'                 => 'Поле ":attribute" должно быть действительным адресом IPv4.',
    'ipv6'                 => 'Поле ":attribute" должно быть действительным адресом IPv6.',
    'json'                 => 'Значение поля ":attribute" должно быть в формате JSON.',
    'lt'                   => [
        'numeric' => 'Значение поля ":attribute" должен быть меньше чем :value.',
        'file'    => 'Файл ":attribute" должен быть меньше чем :value КБ.',
        'string'  => 'Значение поля ":attribute" должно быть меньше чем :value символ.',
        'array'   => 'Значение поля ":attribute" должно содержать меньше чем :value элементов.',
    ],
    'lte'                  => [
        'numeric' => 'Значение поля ":attribute" должен быть меньше или равно :value.',
        'file'    => 'Файл ":attribute" должен быть меньше или равен :value КБ.',
        'string'  => 'Значение поля ":attribute" должно быть меньше или равно :value символ.',
        'array'   => 'Значение поля ":attribute" должно содержать меньше или равно :value элементов.',
    ],
    'max'                  => [
        'numeric' => 'Значение поля ":attribute" не может быть больше чем :max.',
        'file'    => 'Файл ":attribute" не может быть больше чем :max КБ.',
        'string'  => 'Значение поля ":attribute" не может быть больше :max символов.',
        'array'   => 'Значение поля ":attribute" не может содержать больше чем :max элементов.',
    ],
    'mimes'                => 'The ":attribute" must be a file of type: :values.',
    'mimetypes'            => 'The ":attribute" must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'Значение поля ":attribute" не может быть меньше чем :min.',
        'file'    => 'Файл ":attribute" не может быть меньше :min КБ.',
        'string'  => 'Значение поля ":attribute" не может быть меньше чем :min символов.',
        'array'   => 'Значение поля ":attribute" не может содержать меньше чем :min элементов.',
    ],
    'not_in'               => 'Значение поля ":attribute" не должно быть одним из указанных.',
    'not_regex'            => 'Поле ":attribute" не должно соответствовать заданному регулярному выражению.',
    'numeric'              => 'Поле ":attribute" должно иметь корректное числовое или дробное значение.',
    'present'              => 'Поле ":attribute" должно присутствовать.',
    'regex'                => 'Поле ":attribute" должно соответствовать заданному регулярному выражению.',
    'required'             => 'Поле ":attribute" должно иметь непустое значение.',
    'required_if'          => 'Поле ":attribute" должно иметь непустое значение, если поле ":other" равно :value.',
    'required_unless'      => 'Поле ":attribute" должно иметь непустое значение, если поле ":other" не равно :value.',
    'required_with'        => 'Поле ":attribute" должно иметь непустое значение, но только если присутствует любое из перечисленных значений :values.',
    'required_with_all'    => 'Поле ":attribute" должно иметь непустое значение, но только если присутствует все перечисленные значения :values.',
    'required_without'     => 'Поле ":attribute" должно иметь непустое значение, но только если не присутствует любое из перечисленных значений :values.',
    'required_without_all' => 'Поле ":attribute" должно иметь непустое значение, но только если не присутствуют все перечисленные значения :values.',
    'same'                 => 'Поле ":attribute" должно иметь то же значение, что и поле ":other".',
    'size'                 => [
        'numeric' => 'Значение поля ":attribute" must be :size.',
        'file'    => 'Файл ":attribute" must be :size kilobytes.',
        'string'  => 'Значение поля ":attribute" must be :size characters.',
        'array'   => 'Значение поля ":attribute" must contain :size items.',
    ],
    'starts_with'          => 'The ":attribute" must start with one of the following: :values',
    'string'               => 'The ":attribute" must be a string.',
    'timezone'             => 'The ":attribute" must be a valid zone.',
    'unique'               => 'The ":attribute" has already been taken.',
    'uploaded'             => 'The ":attribute" failed to upload.',
    'url'                  => 'The ":attribute" format is invalid.',
    'uuid'                 => 'The ":attribute" must be a valid UUID.',


    'phone_number'        => 'Значение поля ":attribute" должно быть номером телефона.',
    'phone_operator_code' => 'Значение поля ":attribute" содержит не правильный код оператора сотовой связи.',
    'reCaptchaV3'         => 'Значение ":attribute" не прошло проверку.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
