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
        $soloKills      = [];
        $soloScore      = [];
        $soloDate       = [];
        $duoKills       = [];
        $duoScore       = [];
        $duoDate        = [];
        $squadKills     = [];
        $squadScore     = [];
        $squadDate      = [];
        $lifeStats      = [];
        $rankSoloKills  = [];
        $rankSoloTop1   = [];
        $rankSoloScore  = [];
        $rankDuoKills   = [];
        $rankDuoTop1    = [];
        $rankDuoScore   = [];
        $rankSquadKills = [];
        $rankSquadTop1  = [];
        $rankSquadScore = [];
        $config  = $this->get('config');

        $nickname = $this->params()->fromQuery('user', null);
        if ($user = $this->userTable->fetchOne(['nickname' => $nickname])) {
            $lifeStats  = $this->lifetimeTable->fetchOne(['userId' => $user->id]);

            $soloStats  = $this->soloTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($soloStats as $stats) {
                if ($stats->top1) {
                    $soloScore[] = [
                        'y' => (int) $stats->score,
                        'marker' => [
                            'symbol' => 'url(/img/trophy.png)'
                        ]
                    ];
                } else {
                    $soloScore[] = (int) $stats->score;
                }
                $soloKills[] = (int) $stats->kills;
                $soloDate[]  = $stats->updatedAt;
            }

            $duoStats   = $this->duoTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($duoStats as $stats) {
                if ($stats->top1) {
                    $duoScore[] = [
                        'y' => (int) $stats->score,
                        'marker' => [
                            'symbol' => 'url(/img/trophy.png)'
                        ]
                    ];
                } else {
                    $duoScore[] = (int) $stats->score;
                }
                $duoKills[] = (int) $stats->kills;
                $duoDate[]  = $stats->updatedAt;
            }

            $squadStats = $this->squadTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($squadStats as $stats) {
                if ($stats->top1) {
                    $squadScore[] = [
                        'y' => (int) $stats->score,
                        'marker' => [
                            'symbol' => 'url(/img/trophy.png)'
                        ]
                    ];
                } else {
                    $squadScore[] = (int) $stats->score;
                }
                $squadKills[] = (int) $stats->kills;
                $squadDate[]  = $stats->updatedAt;
            }

            $rankStats  = $this->rankSoloTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($rankStats as $stats) {
                $rankSoloKills[] = (int) $stats->rankKills;
                $rankSoloTop1[] = (int) $stats->rankTop1;
                $rankSoloScore[] = (int) $stats->rankScore;
            }

            $rankStats  = $this->rankDuoTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($rankStats as $stats) {
                $rankDuoKills[] = (int) $stats->rankKills;
                $rankDuoTop1[] = (int) $stats->rankTop1;
                $rankDuoScore[] = (int) $stats->rankScore;
            }

            $rankStats  = $this->rankSquadTable->fetchAll(['userId' => $user->id, 'updatedAt > ?' => strtotime('- 14 days')], 'id ASC');
            foreach ($rankStats as $stats) {
                $rankSquadKills[] = (int) $stats->rankKills;
                $rankSquadTop1[] = (int) $stats->rankTop1;
                $rankSquadScore[] = (int) $stats->rankScore;
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
                    'userId'         => $user->id,
                    'soloKills'      => $solo['kills']['value'],
                    'soloMatches'    => $solo['matches']['value'],
                    'soloScore'      => $solo['score']['value'],
                    'soloTop1'       => $solo['top1']['value'],
                    'top10'          => $solo['top10']['value'],
                    'top25'          => $solo['top25']['value'],
                    'duoMatches'     => $duo['matches']['value'],
                    'duoScore'       => $duo['score']['value'],
                    'duoKills'       => $duo['kills']['value'],
                    'duoTop1'        => $duo['top1']['value'],
                    'top5'           => $duo['top5']['value'],
                    'top12'          => $duo['top12']['value'],
                    'squadMatches'   => $squad['matches']['value'],
                    'squadKills'     => $squad['kills']['value'],
                    'squadScore'     => $squad['score']['value'],
                    'squadTop1'      => $squad['top1']['value'],
                    'top3'           => $squad['top3']['value'],
                    'top6'           => $squad['top6']['value'],
                    'rankSoloScore'  => $solo['score']['rank'],
                    'rankSoloKills'  => $solo['kills']['rank'],
                    'rankDuoScore'   => $duo['score']['rank'],
                    'rankDuoKills'   => $duo['kills']['rank'],
                    'rankSquadScore' => $squad['score']['rank'],
                    'rankSquadKills' => $squad['kills']['rank'],
                    'rankSoloTop1'   => $solo['top1']['rank'],
                    'rankDuoTop1'    => $duo['top1']['rank'],
                    'rankSquadTop1'  => $squad['top1']['rank'],
                    'updatedAt'      => date('Y-m-d H:i:s', time()),
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
            'duoDate'   => htmlspecialchars(json_encode($duoDate), ENT_QUOTES, 'UTF-8'),
            'squadScore' => json_encode($squadScore),
            'squadKills' => json_encode($squadKills),
            'soloRankKills' => json_encode($rankSoloKills),
            'soloRankTop1' => json_encode($rankSoloTop1),
            'soloRankScore' => json_encode($rankSoloScore),
            'duoRankKills' => json_encode($rankDuoKills),
            'duoRankTop1' => json_encode($rankDuoTop1),
            'duoRankScore' => json_encode($rankDuoScore),
            'squadRankKills' => json_encode($rankSquadKills),
            'squadRankTop1' => json_encode($rankSquadTop1),
            'squadRankScore' => json_encode($rankSquadScore),
            'squadDate'  => htmlspecialchars(json_encode($squadDate), ENT_QUOTES, 'UTF-8'),
            'nickname'   => $nickname,
        ]);
    }
}
