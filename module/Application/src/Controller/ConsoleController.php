<?php
namespace Application\Controller;

use RuntimeException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Console;
use Zend\Console\Exception\RuntimeException as ConsoleException;
use Zend\Console\ColorInterface as Color;
use Zend\Http\Client;
use Zend\Http\Request;

class ConsoleController extends AbstractController
{
    public function crawlAction()
    {
        $users   = $this->userTable->fetchAll();
        $config  = $this->get('config');
        $console = Console::getInstance();

        foreach ($users as $user) {
            $console->writeLine('Working on user ' . $user->nickname, Color::BLUE);
            $url = $config['api']['fortnite']['url'] . $user->nickname;

            $request = new Request();
            $request->setMethod(Request::METHOD_GET);
            $request->setUri($url);
            $request->getHeaders()->addHeaders([
                'TRN-Api-Key' => $config['api']['fortnite']['key'],
            ]);

            $client = new Client();

            $response = $client->send($request);
            $result = json_decode($response->getBody(), true);

            $lifetime = $this->lifetimeTable->fetchOne([
                'userId' => $user->id
            ], 'id DESC');

            $solo  = $result['stats']['p2'];
            $duo   = $result['stats']['p10'];
            $squad = $result['stats']['p9'];
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

            $lifeStats = $this->lifetimeTable->fetchOne(['userId' => $user->id]);
            if (!$lifeStats) {
                $console->writeLine('New User, creating lifeStats.', Color::MAGENTA);
                $this->lifetimeTable->save($data);
            } else {
                $updatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', $lifetime->updatedAt);
                foreach ($result['recentMatches'] as $values) {
                    $matchDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u', $values['dateCollected']);
                    if ($updatedAt > $matchDate) continue;

                    if ($values['playlist'] == 'p2') {
                        $solo = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top10'     => $values['top10'],
                            'top25'     => $values['top25'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => date('Y-m-d H:i:s', time())
                        ];

                        $this->soloTable->save($diff);
                    } else if ($values['playlist'] == 'p10') {
                        $solo = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top5'      => $values['top5'],
                            'top12'     => $values['top12'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => date('Y-m-d H:i:s', time())
                        ];

                        $this->duoTable->save($diff);
                    } else if ($values['playlist'] == 'p9') {
                        $solo = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top3'      => $values['top3'],
                            'top6'      => $values['top6'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => date('Y-m-d H:i:s', time())
                        ];

                        $this->squadTable->save($diff);
                    }
                }
                $data += ['id' => $lifeStats->id];
                $this->lifetimeTable->save($data);
            }
            $console->writeLine('Done.', Color::BLUE);
            $console->writeLine('waiting 2 secs.', Color::GREEN);
            sleep(2);
        }
    }
}