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
    'postService' => [
        'className' => '\App\Services\PostService',
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
    'commentService' => [
        'className' => '\App\Services\CommentService',
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
    'notificationService' => [
        'className' => '\App\Services\NotificationService',
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
    'transactionService' => [
        'className' => '\App\Services\TransactionService',
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
