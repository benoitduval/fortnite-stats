<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Application\TableGateway;
use Application\Service\AuthenticationService;

class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.0';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                // 'Cache\Adapter\Memcached' => function ($serviceManager) {
                //     $memcached = new \Memcached($serviceManager->get('Cache\Adapter\MemcachedOptions'));
                //     return $memcached;
                // },
                // 'Cache\Adapter\MemcachedOptions' => function ($serviceManager) {
                //     return new \pMemcachedOptions(array(
                //         'ttl'           => 60 * 60 * 24 * 7, // 1 week
                //         'namespace'     => 'cache_listener',
                //         'key_pattern'   => null,
                //         'readable'      => true,
                //         'writable'      => true,
                //         'servers'       => 'localhost',
                //     ));
                // },
            ],
        ];
    }
}
