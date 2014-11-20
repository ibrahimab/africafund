<?php
require_once 'Zend/Loader/AutoloaderFactory.php';

Zend\Loader\AutoloaderFactory::factory([

    'Zend\Loader\StandardAutoloader' => [
        'autoregister_zf' => true,
    ],
]);