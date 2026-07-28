<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    */
    
   'pdf' => [
        'enabled' => true,
        // 🌟 ដូរមកប្រើ Path របស់ Windows វិញ (ត្រូវតែមានសញ្ញា " " ពីក្រៅអក្សរ C: ផងកុំឱ្យ Error ដកឃ្លា)
        'binary'  => '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe"', 
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],
    
    'image' => [
        'enabled' => true,
        // 🌟 ដូរទីតាំង Image ដូចគ្នា
        'binary'  => env('WKHTML_IMG_BINARY', '/opt/homebrew/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];