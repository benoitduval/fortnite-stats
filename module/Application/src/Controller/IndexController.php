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
                $result[$category]['kills'] = [];
                $result[$category]['dates'] = [];
                $statistics  = $this->$table->fetchAll($options, 'id ASC');
                $repartitionKills = ['0' => 0, '1-3' => 0, '4-6' => 0, '7-9' => 0, '10+' => 0];
                $repartitionTop1  = ['0' => 0, '1-3' => 0, '4-6' => 0, '7-9' => 0, '10+' => 0];
                foreach ($statistics as $stats) {
                    if ($stats->top1) {
                        $result[$category]['kills'][] = [
                            'y' => (int) $stats->kills,
                            'marker' => [
                                'symbol' => 'url(/img/trophy.png)'
                            ]
                        ];
                        if (!$stats->kills) {
                            $repartitionTop1['0'] ++;
                        } else if ($stats->kills >= 1 && $stats->kills <= 3 ) {
                            $repartitionTop1['1-3'] ++;
                        } else if ($stats->kills >= 4 && $stats->kills <= 6 ) {
                            $repartitionTop1['4-6'] ++;
                        } else if ($stats->kills >= 7 && $stats->kills <= 9 ) {
                            $repartitionTop1['7-9'] ++;
                        } else {
                            $repartitionTop1['10+'] ++;
                        }
                    } else {
                        if (!$stats->kills) {
                            $repartitionKills['0'] ++;
                        } else if ($stats->kills >= 1 && $stats->kills <= 3 ) {
                            $repartitionKills['1-3'] ++;
                        } else if ($stats->kills >= 4 && $stats->kills <= 6 ) {
                            $repartitionKills['4-6'] ++;
                        } else if ($stats->kills >= 7 && $stats->kills <= 9 ) {
                            $repartitionKills['7-9'] ++;
                        } else {
                            $repartitionKills['10+'] ++;
                        }
                        $result[$category]['kills'][] = (int) $stats->kills;
                        $result[$category]['repartition']['kills'] = (int) $stats->kills;
                    }
                    $result[$category]['dates'][] = $stats->updatedAt;
                }
                $repartition = [];
                foreach ($repartitionKills as $name => $value) {
                    $repartition[] = ['name' => $name, 'y' => $value];
                }
                $result[$category]['repartition']['kills'] = $repartition;
                $repartition = [];
                foreach ($repartitionTop1 as $name => $value) {
                    $repartition[] = ['name' => $name, 'y' => $value];
                }
                $result[$category]['repartition']['top1'] = $repartition;
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
