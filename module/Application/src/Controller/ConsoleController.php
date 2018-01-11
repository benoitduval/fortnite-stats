<?php
namespace Application\Controller;

use RuntimeException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Math\Rand;
use Zend\Crypt\Password\Bcrypt;
use Application\TableGateway;
use Application\Model;
use Application\Service;
use Zend\Console\Console;
use Zend\Console\Exception\RuntimeException as ConsoleException;
use Zend\Console\ColorInterface as Color;
use Zend\Http\Client;
use Zend\Http\Request;

class ConsoleController extends AbstractController
{
    public function crawlAction()
    {
        $users = $this->userTable->fetchAll();
        $config = $this->get('config');
        foreach ($users as $user) {
            $url = $config['api']['fortnite']['url'] . $user->nickname;

            $request = new Request();
            $request->setMethod(Request::METHOD_GET);
            $request->setUri($url);
            $request->getHeaders()->addHeaders([
                'TRN-Api-Key' => $config['api']['fortnite']['key'],
            ]);

            $client = new Client();

            $response = $client->send($request);
            $data = json_decode($response->getBody(), true);

            $stats = $data['stats']['p2'];
            $data = [
                'userId'   => $user->id,
                'top1'     => $stats['top1']['value'],
                'top10'    => $stats['top10']['value'],
                'top25'    => $stats['top25']['value'],
                'matches'  => $stats['matches']['value'],
                'kills'    => $stats['kills']['value'],
                'score'    => $stats['score']['value'],
            ];

            $lifeStats = $this->lifetimeTable->fetchOne(['userId' => $user->id]);
            if (!$lifeStats) {
                $this->lifetimeTable->save($data);
            } else {
                if ($data['matches'] != $lifeStats->matches) {
                    $diff = [
                        'userId'  => $user->id,
                        'top1'    => $stats['top1']['value'] - $lifeStats->top1,
                        'top10'   => $stats['top10']['value'] - $lifeStats->top10,
                        'top25'   => $stats['top25']['value'] - $lifeStats->top25,
                        'matches' => $stats['matches']['value'] - $lifeStats->matches,
                        'kills'   => $stats['kills']['value'] - $lifeStats->kills,
                        'score'   => $stats['score']['value'] - $lifeStats->score,
                    ];

                    $this->statsTable->save($diff);

                    $data += ['id' => $lifeStats->id];
                    $this->lifetimeTable->save($data);
                }
            }
        }
    }
}