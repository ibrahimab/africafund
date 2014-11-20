<?php

$env = getenv('APP_ENV') ?: 'production';
if (defined('APP_ENV') === false) {
    define('APP_ENV', $env);
}

if ($env == 'development') {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (defined('APP_BASE_DOMAIN') === false) {
        define('APP_BASE_DOMAIN', 'BASE_DOMAIN_NO_WWWW');
    }

} else if ($env == 'production') {

    if (defined('APP_BASE_DOMAIN') === false) {
        define('APP_BASE_DOMAIN', 'BASE_DOMAIN_PRODUCTION_NO_WWW');
    }

    ini_set('display_errors', 0);
    register_shutdown_function(function() {

        if ($error = error_get_last()) {

            // relative to project
            chdir(dirname(__DIR__));

            $folder = 'data/error/' . date('m-Y');
            if (false === file_exists($folder)) {
                mkdir($folder);
            }

            $logger    = new Zend\Log\Logger;
            $writer    = new Zend\Log\Writer\Stream($folder . '/' . date('d-m-Y') . '.log');

            $logger->addWriter($writer);

            $logger->emerg(

                sprintf(
                    "[EMERG] TYPE [%d] -- %s -- on line [%d] in file %s",
                    $error['type'],
                    $error['message'],
                    $error['line'],
                    $error['file']
                )
            );

            $resolver = new \Zend\View\Resolver\TemplatePathStack(array(
                'script_paths' => array('modules/Africafund/views'),
            ));

            $renderer = new \Zend\View\Renderer\PhpRenderer();
            $renderer->setCanRenderTrees(true);
            $renderer->setResolver($resolver);

            $view = new \Zend\View\Model\ViewModel();
            $view->setTemplate('errors/error/clean');

            $layout = new \Zend\View\Model\ViewModel(['content' => $renderer->render($view)]);
            $layout->setTemplate('errors/layout/layout');

            echo $renderer->render($layout);
            exit;
        }
    });
}