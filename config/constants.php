<?php

return [

    /*
    |--------------------------------------------------------------------------
    | This configuration file is related to validation constants
    |--------------------------------------------------------------------------
    |
    | Here you can define application wise validation constants
    |
    */

    'max_image_size' => env('MAX_IMAGE_SIZE', 8192), // 8192 KB = 8 MB
    'max_video_size' => env('MAX_VIDEO_SIZE', 51200), // 51200 KB = 20 MB
];
