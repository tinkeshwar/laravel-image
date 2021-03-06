<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Storage For File Upload
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'image_storage' => env('IMAGE_STORAGE', config('filesystems.default')),

    /*
    |--------------------------------------------------------------------------
    | Default Storage For Image Cache
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'image_cache_storage' => env('IMAGE_CACHE_STORAGE', config('filesystems.default')),

    /*
    |--------------------------------------------------------------------------
    | Default Output File Extension
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    'image_extension' => env('IMAGE_EXTENSION', '.webp') //.webp and .png supported
];
