<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Application\Model;
use Application\TableGateway;


class AbstractController extends AbstractActionController
{
    protected $_container;

    public function __construct(ContainerInterface $container, $tables)
    {
        $this->_container       = $container;

        foreach ($tables as $name => $obj) {
            $name .= 'Table';
            $this->$name = $obj;
        }
    }

    public function get($name, $options = [])
    {
        return $this->_container->get($name);
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $config = $this->get('config');
        $this->layout()->vCss   = $config['version']['css'];
        $this->layout()->vJs    = $config['version']['js'];
        return parent::onDispatch($e);
    }
}
