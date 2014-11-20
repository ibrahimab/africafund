<?php

return [
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'errors/error/404',
    'exception_template'       => 'errors/error/index',
    'template_map'             => [],
    'template_path_stack'      => [

        'Africafund' => Africafund\MODULE::PATH . '/views',
    ],
    'strategies' => ['ViewJsonStrategy'],
];