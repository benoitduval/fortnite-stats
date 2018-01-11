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
            $data = json_decode($response->getBody(), true);

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

            $lifeStats = $this->lifetimeTable->fetchOne(['userId' => $user->id]);
            if (!$lifeStats) {
                $console->writeLine('New User, creating lifeStats.', Color::MAGENTA);
                $this->lifetimeTable->save($data);
            } else {
                if ($data['soloMatches'] != $lifeStats->soloMatches) {
                    $console->writeLine('Updating Solo Stats.', Color::LIGHT_BLUE);
                    $diff = [
                        'userId'    => $user->id,
                        'top1'      => $solo['top1']['value'] - $lifeStats->soloTop1,
                        'top10'     => $solo['top10']['value'] - $lifeStats->top10,
                        'top25'     => $solo['top25']['value'] - $lifeStats->top25,
                        'matches'   => $solo['matches']['value'] - $lifeStats->soloMatches,
                        'kills'     => $solo['kills']['value'] - $lifeStats->soloKills,
                        'score'     => $solo['score']['value'] - $lifeStats->soloScore,
                        'updatedAt' => date('Y-m-d H:i:s', time()),
                    ];

                    $this->soloTable->save($diff);

                    $data += ['id' => $lifeStats->id];
                    $this->lifetimeTable->save($data);
                }

                if ($data['duoMatches'] != $lifeStats->duoMatches) {
                    $console->writeLine('Updating Duo Stats.', Color::LIGHT_BLUE);
                    $diff = [
                        'userId'    => $user->id,
                        'top1'      => $duo['top1']['value'] - $lifeStats->duoTop1,
                        'top5'      => $duo['top5']['value'] - $lifeStats->top5,
                        'top12'     => $duo['top12']['value'] - $lifeStats->top12,
                        'matches'   => $duo['matches']['value'] - $lifeStats->duoMatches,
                        'kills'     => $duo['kills']['value'] - $lifeStats->duoKills,
                        'score'     => $duo['score']['value'] - $lifeStats->duoScore,
                        'updatedAt' => date('Y-m-d H:i:s', time()),
                    ];

                    $this->duoTable->save($diff);

                    $data += ['id' => $lifeStats->id];
                    $this->lifetimeTable->save($data);
                }

                if ($data['squadMatches'] != $lifeStats->squadMatches) {
                    $console->writeLine('Updating Squad Stats.', Color::LIGHT_BLUE);
                    $diff = [
                        'userId'    => $user->id,
                        'top1'      => $squad['top1']['value'] - $lifeStats->squadTop1,
                        'top3'      => $squad['top3']['value'] - $lifeStats->top3,
                        'top6'      => $squad['top6']['value'] - $lifeStats->top6,
                        'matches'   => $squad['matches']['value'] - $lifeStats->squadMatches,
                        'kills'     => $squad['kills']['value'] - $lifeStats->squadKills,
                        'score'     => $squad['score']['value'] - $lifeStats->squadScore,
                        'updatedAt' => date('Y-m-d H:i:s', time()),
                    ];

                    $this->squadTable->save($diff);

                    $data += ['id' => $lifeStats->id];
                    $this->lifetimeTable->save($data);
                }
            }
            $console->writeLine('Done.', Color::BLUE);
            $console->writeLine('waiting 2 secs.', Color::GREEN);
            sleep(2);
        }
    }
}