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

    }

    public function statsAction()
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
        $lifeStats  = [];
        $config  = $this->get('config');

        $nickname = $this->params()->fromQuery('user', null);
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
            foreach ($squadStats as $stats) {
                $squadKills[] = (int) $stats->kills;
                $squadScore[] = (int) $stats->score;
                $squadDate[]  = $stats->updatedAt;
            }

        } else {

            $url = $config['api']['fortnite']['url'] . $nickname;
            $request = new Request();
            $request->setMethod(Request::METHOD_GET);
            $request->setUri($url);
            $request->getHeaders()->addHeaders([
                'TRN-Api-Key' => $config['api']['fortnite']['key'],
            ]);

            $client = new Client();

            $response = $client->send($request);
            $data = json_decode($response->getBody(), true);

            if (isset($data['stats'])) {
                $user = $this->userTable->save([
                    'nickname' => $nickname,
                    'createdAt' => date('Y-m-d H:i:s', time()),
                    'updatedAt' => date('Y-m-d H:i:s', time()),
                ]);

                $solo  = $data['stats']['p2'];
                $duo   = $data['stats']['p10'];
                $squad = $data['stats']['p9'];
                $data = [
                    'userId'       => $user->id,
                    'soloKills'    => $solo['kills']['value'],
                    'soloMatches'  => $solo['matches']['value'],
                    'soloScore'    => $solo['score']['value'],
                    'soloTop1'     => $solo['top1']['value'],
                    'top10'        => $solo['top10']['value'],
                    'top25'        => $solo['top25']['value'],
                    'duoMatches'   => $duo['matches']['value'],
                    'duoScore'     => $duo['score']['value'],
                    'duoKills'     => $duo['kills']['value'],
                    'duoTop1'      => $duo['top1']['value'],
                    'top5'         => $duo['top5']['value'],
                    'top12'        => $duo['top12']['value'],
                    'squadMatches' => $squad['matches']['value'],
                    'squadKills'   => $squad['kills']['value'],
                    'squadScore'   => $squad['score']['value'],
                    'squadTop1'    => $squad['top1']['value'],
                    'top3'         => $squad['top3']['value'],
                    'top6'         => $squad['top6']['value'],
                    'updatedAt'    => date('Y-m-d H:i:s', time()),
                ];
                $lifeStats = $this->lifetimeTable->save($data);
            }
        }

        if (!$user) $this->redirect()->toUrl('/');

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
