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
        $result  = [];
        $config  = $this->get('config');
        $from    = $this->params()->fromQuery('from', null);
        $to      = $this->params()->fromQuery('to', null);

        $nickname = $this->params()->fromQuery('user', null);
        if ($user = $this->userTable->fetchOne(['nickname' => $nickname])) {
            $lifeStats  = $this->lifetimeTable->fetchOne(['userId' => $user->id]);

            $options['userId'] = $user->id;
            if ($from) $options['updatedAt >= ?'] = $from . ' 00:00:00';
            if ($to) $options['updatedAt <= ?']   = $to . ' 23:59:59';

            foreach (['solo', 'duo', 'squad'] as $category) {
                $table = $category . 'Table';
                $statistics  = $this->$table->fetchAll($options, 'id ASC');
                foreach ($statistics as $stats) {
                    if ($stats->top1) {
                        $result[$category]['kills'][] = [
                            'y' => (int) $stats->kills,
                            'marker' => [
                                'symbol' => 'url(/img/trophy.png)'
                            ]
                        ];
                    } else {
                        $result[$category]['kills'][] = (int) $stats->kills;
                    }
                    $result[$category]['dates'][] = $stats->updatedAt;
                }

                $rankTable = 'rank' . ucfirst($category) . 'Table';
                $rankStats  = $this->$rankTable->fetchAll($options, 'id ASC');
                foreach ($rankStats as $stats) {
                    $result[$category]['rank']['kills'][] = (int) $stats->rankKills;
                    $result[$category]['rank']['top1'][] = (int) $stats->rankTop1;
                }
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
            'result'         => $result,
            'lifeStats'      => $lifeStats,
            'nickname'       => $nickname,
        ]);
    }
}
