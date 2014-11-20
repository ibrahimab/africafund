<?php
namespace Africafund;
use       Zend\Mvc\ModuleRouteListener;
use       Zend\Mvc\MvcEvent;
use       Zend\Session\Container;

class Module {

    const PATH = __DIR__;
    protected $config;
    protected $service_manager;

    public function onBootstrap(MvcEvent $e) {

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // logging several error events
        $eventManager->attach('dispatch.error', array($this, 'moduleLogger'));
        $eventManager->attach('render.error', array($this, 'moduleLogger'));

        // start session
        $this->startSession($e);
    }

    public function getConfig() {

        if (null === $this->config) {
            $this->config = include_once self::PATH . '/config/module.php';
        }

        return $this->config;
    }

    public function getServiceConfig() {

        if (null === $this->service_manager) {
            $this->service_manager = include_once self::PATH . '/config/service_manager.php';
        }

        return $this->service_manager;
    }

    public function getAutoloaderConfig() {

        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__  => self::PATH,
                ),
            ),
        );
    }

    public function moduleLogger(MvcEvent $e) {

        /** @var \Zend\Log\Logger $logger */
        /** @var \Exception $exceptions */
        /** @var \Exception $exception */

        $logger     = $e->getApplication()->getServiceManager()->get('Logger');
        if ($exception = $e->getResult()->exception) {

            do {

                $logger->crit(

                    sprintf(
                        "%s:%d %s (%d) [%s]\nTrace:\n%s",
                        $exception->getFile(),
                        $exception->getLine(),
                        $exception->getMessage(),
                        $exception->getCode(),
                        get_class($exception),
                        $exception->getTraceAsString()
                    )
                );

            } while ($exception = $exception->getPrevious());

        } else {

            $e->getApplication()->getServiceManager()->get('Logger')->crit(

                sprintf(
                    "[error] Application error: %s\n",
                    $e->getError()
                )
            );
        }
    }

    public function startSession(MvcEvent $e) {

        $session = $e->getApplication()->getServiceManager()->get('Session\Manager');
        $session->start();

        $container = new Container('SESSION_CONTAINER');
        if (!isset($container->init)) {

            $session->regenerateId(true);
            $container->init = 1;
        }
    }
}