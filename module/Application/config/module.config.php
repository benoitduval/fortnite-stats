<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Cache;

return [

    'service_manager' => [
        'factories' => [
            Service\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\MailService::class           => Service\Factory\MailServiceFactory::class,
            Service\Map::class                   => Service\Factory\MapServiceFactory::class,
        ],

        'abstract_factories' => [
            Factory\TableGatewayFactory::class,
            Cache\Service\StorageCacheAbstractServiceFactory::class,
        ],
    ],

    'controllers' => [
        'abstract_factories' => [
            Factory\ControllerFactory::class,
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'stats' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/stats',
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'stats',
                    ],
                ],
            ],
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'crawl' => [
                    'options' => [
                        'route'    => 'crawl [--verbose|-v]',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action'     => 'crawl',
                        ],
                    ],
                ],
                'lifetime-update' => [
                    'options' => [
                        'route'    => 'lifetime-update [--verbose|-v]',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action'     => 'lifetime-update',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
