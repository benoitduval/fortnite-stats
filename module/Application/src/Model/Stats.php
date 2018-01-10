<?php
namespace Application\Model;

use Application\Tablegateway;

class Stats extends AbstractModel
{
    protected $_id = null;
    protected $_userId = null;
    protected $_top1 = null;
    protected $_top10 = null;
    protected $_top25 = null;
    protected $_matches = null;
    protected $_kills = null;
    protected $_score = null;
}