<?php
// ImageManager

return [

    'disks' => [
        'url' => env('APP_URL') . '/storage',
        'disk' => storage_path('app/public'),
        'path' => '/storage/app/public/',
    ],

    // save resize cache image for next size [width,height]
    'cache' => [
        [100, 80],
        [200, 160],
    ],

    'mime_types' => [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
    ],

    'useragent' => [
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11',
    ],

    'pipes' => [
        \GarbuzIvan\ImageManager\Pipes\Optimizer::class,
    ],

    'transport' => \GarbuzIvan\ImageManager\Transport\EloquentTransport::class,

];
