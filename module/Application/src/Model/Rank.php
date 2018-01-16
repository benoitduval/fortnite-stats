<?php
namespace Application\Model;

use Application\Tablegateway;

class Rank extends AbstractModel
{
    protected $_id =null;
    protected $_userId = null;
    protected $_rankSoloScore = null;
    protected $_rankSoloKills = null;
    protected $_rankSoloTop1 = null;
    protected $_rankDuoScore = null;
    protected $_rankDuoKills = null;
    protected $_rankDuoTop1 = null;
    protected $_rankSquadScore = null;
    protected $_rankSquadKills = null;
    protected $_rankSquadTop1 = null;
    protected $_updatedAt = null;
}