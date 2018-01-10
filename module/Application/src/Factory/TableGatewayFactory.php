<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class TableGatewayFactory implements AbstractFactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        preg_match('/Application.TableGateway.([A-Za-z]*)/', $requestedName, $matches);
        list(,$name) = $matches;

        $dbAdapter          = $container->get(AdapterInterface::class);
        $resultSetPrototype = new ResultSet();
        $model = '\\Application\\Model\\' . $name;
        $resultSetPrototype->setArrayObjectPrototype(new $model());
        $tableGateway = new TableGateway(lcfirst($name), $dbAdapter, null, $resultSetPrototype);
        return new $requestedName($tableGateway, $container);
    }

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return preg_match('/TableGateway/', $requestedName);
    }
}