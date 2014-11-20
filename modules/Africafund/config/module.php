<?php

return [
    'router' => [
        'routes' => include_once Africafund\Module::PATH . '/config/routes.php',
    ],
    //'translator'      => include_once Africafund\Module::PATH . '/config/translator.php',
    'controllers'     => include_once Africafund\Module::PATH . '/config/controllers.php',
    'view_manager'    => include_once Africafund\Module::PATH . '/config/view_manager.php',
    'view_helpers'    => include_once Africafund\Module::PATH . '/config/view_helpers.php',
    // custom configuration
    'app'             => include_once Africafund\Module::PATH . '/config/app.php',
];