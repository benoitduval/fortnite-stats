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
        $nickname = $this->params('user', null);
        if ($user = $this->userTable->fetchOne(['nickname' => $nickname])) {
            $lifeStats = $this->lifetimeTable->fetchOne(['userId' => $user->id]);

            $url = 'https://api.fortnitetracker.com/v1/profile/pc/' . $nickname;

            return new ViewModel([
                'lifeStats'   => $lifeStats,
            ]);
        }
    }
}
