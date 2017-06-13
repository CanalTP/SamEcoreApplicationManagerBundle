<?php

namespace CanalTP\SamEcoreApplicationManagerBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Throw an exception if user is not allowed
 *
 * @author Kévin Ziemianski <kevin.ziemianski@canaltp.fr>
 */
class CheckAccess
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->container->get('security.context')->getToken();
        if (is_null($token) || $event->getRequestType() != HttpKernel::MASTER_REQUEST) {
            return;
        }
        $user = $token->getUser();
        $route = $event->getRequest()->attributes->get('_route');
        $routeAuthorized = array(
            'fos_user_security_login',
            'sam_user_edit_profil',
            'canal_tp_sam_ecore_application_manager_choose_application',
            'fos_user_registration_confirmed',
            'fos_oauth_server_authorize',
        );
        $appService = $this->container->get('canal_tp_sam.application.finder');

        if (!$user instanceof UserInterface) {
            return;
        }
        $userApps = $appService->getUserApps($user);

        $matches = array();
        preg_match('/\/([a-z0-9]*)/', $event->getRequest()->getPathInfo(), $matches);
        if (!isset($matches[1]) || $matches[1] == '') {
            return;
        }

        if (!in_array($matches[1], $userApps) && !in_array($route, $routeAuthorized)) {
            throw new AccessDeniedException();
        }

        return true;
    }
}
