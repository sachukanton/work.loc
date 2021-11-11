<?php

    Route::match([
        'get',
        'post'
    ],'/', [
        'as'   => 'entity_load',
        'uses' => 'Callback\LoadController@load'
    ]);
