<?php

return [
     'userService' => [
         'className' => '\App\Services\UserService',
         'arguments' => [
            [
                'type' => 'service',
                'name' => 'response',
            ],
            [
                'type'  => 'parameter',
                'value' => true,
            ],
        ],
     ],
];
