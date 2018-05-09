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
                $matches = array_reverse($result['recentMatches']);
                foreach ($matches as $values) {
                    if ($values['matches'] > 1) continue;
                    if (!($matchDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u', $values['dateCollected']))) {
                        $matchDate = \DateTime::createFromFormat('Y-m-d\TH:i:s', $values['dateCollected']);
                    }

                    if ($values['playlist'] == 'p2') {
                        if (!isset($soloUpdate)) {
                            $lastMatch = $this->soloTable->fetchOne(['userId' => $user->id], 'id DESC');
                            $soloUpdate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastMatch->updatedAt);
                        }
                        if ($matchDate < $soloUpdate) continue;
                        if ($values['top1']) $values['top10'] = $values['top25'] = 0;
                        if ($values['top10']) $values['top25'] = 0;
                        $solo = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top10'     => $values['top10'],
                            'top25'     => $values['top25'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => $matchDate->format('Y-m-d H:i:s')
                        ];
                        $soloUpdate = $matchDate;
                        $this->soloTable->save($solo);
                    } else if ($values['playlist'] == 'p10') {
                        if (!isset($duoUpdate)) {
                            $lastMatch = $this->duoTable->fetchOne(['userId' => $user->id], 'id DESC');
                            $duoUpdate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastMatch->updatedAt);
                        }
                        if ($matchDate < $duoUpdate) continue;
                        if ($values['top1']) $values['top5'] = $values['top12'] = 0;
                        if ($values['top5']) $values['top12'] = 0;
                        $duo = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top5'      => $values['top5'],
                            'top12'     => $values['top12'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => $matchDate->format('Y-m-d H:i:s')
                        ];
                        $duoUpdate = $matchDate;
                        $this->duoTable->save($duo);
                    } else if ($values['playlist'] == 'p9') {
                        if (!isset($squadUpdate)) {
                            $lastMatch = $this->duoTable->fetchOne(['userId' => $user->id], 'id DESC');
                            $squadUpdate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastMatch->updatedAt);
                        }
                        if ($matchDate < $squadUpdate) continue;
                        if ($values['top1']) $values['top3'] = $values['top6'] = 0;
                        if ($values['top3']) $values['top6'] = 0;
                        $squad = [
                            'userId'    => $user->id,
                            'top1'      => $values['top1'],
                            'top3'      => $values['top3'],
                            'top6'      => $values['top6'],
                            'matches'   => $values['matches'],
                            'kills'     => $values['kills'],
                            'score'     => $values['score'],
                            'updatedAt' => $matchDate->format('Y-m-d H:i:s')
                        ];

                        $squadUpdate = $matchDate;
                        $this->squadTable->save($squad);
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