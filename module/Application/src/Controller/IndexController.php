<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\TableGateway;
use Application\Service\AuthenticationService;
use Application\Service\StorageCookieService;
use Application\Model;
use Application\Service\MailService;
use Application\Form\SignIn;
use Zend\Http\Request;
use Zend\Http\Client;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        \Zend\Debug\Debug::dump("");die;
    }

    public function userAction()
    {
        $soloKills  = [];
        $soloScore  = [];
        $soloDate   = [];
        $duoKills   = [];
        $duoScore   = [];
        $duoDate    = [];
        $squadKills = [];
        $squadScore = [];
        $squadDate  = [];

        $nickname = $this->params('user', null);
        if ($user = $this->userTable->fetchOne(['nickname' => $nickname])) {
            $lifeStats  = $this->lifetimeTable->fetchOne(['userId' => $user->id]);
            $soloStats  = $this->soloTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($soloStats as $stats) {
                $soloKills[] = (int) $stats->kills;
                $soloScore[] = (int) $stats->score;
                $soloDate[]  = $stats->updatedAt;
            }

            $duoStats   = $this->duoTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($duoStats as $stats) {
                $duoKills[] = (int) $stats->kills;
                $duoScore[] = (int) $stats->score;
                $duoDate[]  = $stats->updatedAt;
            }

            $squadStats = $this->squadTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($duoStats as $stats) {
                $squadKills[] = (int) $stats->kills;
                $squadScore[] = (int) $stats->score;
                $squadDate[]  = $stats->updatedAt;
            }

            return new ViewModel([
                'lifeStats'  => $lifeStats,
                'soloScore'  => json_encode($soloScore),
                'soloKills'  => json_encode($soloKills),
                'soloDate'   => htmlspecialchars(json_encode($soloDate), ENT_QUOTES, 'UTF-8'),
                'duoScore'   => json_encode($duoScore),
                'duoKills'   => json_encode($duoKills),
                'duoDates'   => htmlspecialchars(json_encode($duoDate), ENT_QUOTES, 'UTF-8'),
                'squadScore' => json_encode($squadScore),
                'squadKills' => json_encode($squadKills),
                'squadDate'  => htmlspecialchars(json_encode($squadDate), ENT_QUOTES, 'UTF-8'),
                'nickname'   => $nickname,
            ]);
        }
    }
}
