<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Application\Service\AuthenticationService;
use Application\TableGateway;

class ControllerFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = null;
        $tables = [
            'user'     => $container->get(TableGateway\User::class),
            'lifetime' => $container->get(TableGateway\Lifetime::class),
            'stats'    => $container->get(TableGateway\Stats::class),
        ];

        return new $requestedName($container, $tables);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }
}