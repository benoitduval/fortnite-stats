<?php
namespace Application\Model;

use Application\Tablegateway;

class Lifetime extends AbstractModel
{
    protected $_id =null;
    protected $_userId = null;
    protected $_soloTop1 = null;
    protected $_duoTop1 = null;
    protected $_squadTop1 = null;
    protected $_top3 = null;
    protected $_top5 = null;
    protected $_top6 = null;
    protected $_top10 = null;
    protected $_top12 = null;
    protected $_top25 = null;
    protected $_soloMatches = null;
    protected $_duoMatches = null;
    protected $_squadMatches = null;
    protected $_soloKills = null;
    protected $_duoKills = null;
    protected $_squadKills = null;
    protected $_soloScore = null;
    protected $_duoScore = null;
    protected $_squadScore = null;
    protected $_updatedAt = null;
}