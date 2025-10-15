<?php

return [
    // In config/broadcasting.php

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => '6a541d1680d3ac334691',
            'secret' => '5244fd1c76f22573524c',
            'app_id' => '2064053',
            'options' => [
                'cluster' => 'ap3',
                'encrypted' => true,
                'useTLS' => true,
            ],
            // ... rest of array
        ],
    ],
];
