<?php
namespace Application\Model;

use Application\Tablegateway;

class Duo extends AbstractModel
{
    protected $_id = null;
    protected $_userId = null;
    protected $_top1 = null;
    protected $_top5 = null;
    protected $_top12 = null;
    protected $_matches = null;
    protected $_kills = null;
    protected $_score = null;
    protected $_updatedAt = null;
    protected $_rankScore = null;
    protected $_rankKills = null;
    protected $_rankTop1 = null;
}