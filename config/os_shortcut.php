<?php
    return [
        'form'        => [
            'model'   => App\Models\Form\Forms::class,
            'primary' => 'id',
        ],
        'form_button' => [
            'model'   => App\Models\Form\Forms::class,
            'primary' => 'id',
        ],
        'banner'      => [
            'model'   => App\Models\Components\Banner::class,
            'primary' => 'id',
        ],
        'block'       => [
            'model'   => App\Models\Components\Block::class,
            'primary' => 'id',
        ],
        'advantage'   => [
            'model'   => App\Models\Components\Advantage::class,
            'primary' => 'id',
        ],
        'slider'      => [
            'model'   => App\Models\Components\Slider::class,
            'primary' => 'id',
        ],
        'products'    => [
            'model'    => App\Models\Shop\Product::class,
            'primary'  => 'id',
            'multiple' => TRUE
        ],
    ];