<?php

return [
    'home' => [
        'type'    => 'Literal',
        'options' => [
            'route'    => '/',
            'defaults' => [
                '__NAMESPACE__' => 'Africafund\Controller',
                'controller'    => 'Africafund\Controller\Home',
                'action'        => 'index',
            ],
        ],
    ],
];