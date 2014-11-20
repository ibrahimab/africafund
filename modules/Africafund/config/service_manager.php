<?php
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceManager;
use Zend\Cache\StorageFactory           as CacheFactory;
use Zend\Authentication\Storage\Session as AuthenticationSessionStorage;
use Zend\Session\Container              as SessionContainer;
use Zend\Session\SaveHandler\Cache      as SessionCacheSaveHandler;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionArrayStorage;

return [
    'abstract_factories' => [
        'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        'Zend\Db\Adapter\AdapterAbstractServiceFactory',
    ],
    'factories' => [

        'Cache\Redis' => function() {

            return CacheFactory::factory([

                'adapter' => [
                    'name'    => 'redis',
                    'options' => [
                        'server' => [
                            'host' => '127.0.0.1',
                            'port' => 6379,
                        ],
                    ],
                ],
            ]);
        },

        'Session\Container' => function() {
            return new Zend\Session\Container('Session');
        },

        'Session\Manager' => function(ServiceManager $serviceManager) {

            $sessionConfig = new SessionConfig;
            $sessionConfig->setOptions([
                'name' => 'Session',
            ]);

//            /** @var \Zend\Cache\Storage\Adapter\Redis $redisCache */
//            $redisCache       = $serviceManager->get('Cache\Redis');
//            $saveHandler      = new SessionCacheSaveHandler($redisCache);
            $sessionStorage   = new SessionArrayStorage;
            $sessionManager   = new SessionManager($sessionConfig, $sessionStorage);
            //$sessionManager->setSaveHandler($saveHandler);

            SessionContainer::setDefaultManager($sessionManager);
            return $sessionManager;
        },

        'Logger' => function() {

            $folder = 'data/logs/' . date('m-Y');
            if (false === file_exists($folder)) {
                mkdir($folder);
            }

            $logger = new Zend\Log\Logger;
            $writer = new Zend\Log\Writer\Stream($folder . '/' . date('d-m-Y') . '.log');

            $logger->addWriter($writer);
            return $logger;
        },

        'AuthStorage' => function() {
            return new AuthenticationSessionStorage('Auth');
        },

        'AuthService' => function(ServiceManager $serviceManager) {

            /** @var \Zend\Db\Adapter\Adapter $db */
            $db             = $serviceManager->get('Db');

            /** @var \Zend\Authentication\Storage\Session $authStorage */
            $authStorage    = $serviceManager->get('AuthStorage');

            $authDb         = new CredentialTreatmentAdapter($db, 'users', 'id', 'id', '?');
            $authService    = new AuthenticationService;

            $authService->setAdapter($authDb);
            $authService->setStorage($authStorage);

            return $authService;
        },

        'Cache\Default' => function() {

            return [

                'adapter' => [
                    'name' => 'filesystem',
                    'options' => [
                        'ttl'        => 3600,
                        'cache_dir'  => 'data/cache/hour',
                        'namespace'  => 'cache-namespace',
                    ],
                ],
                'plugins' => [
                    'serializer',
                ],
            ];
        },
    ],
    'aliases' => [
        'translator' => 'MvcTranslator',
    ],
];
