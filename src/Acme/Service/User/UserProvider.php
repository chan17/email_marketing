<?php
namespace Acme\Service\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Acme\Service\Common\ServiceKernel;
use Acme\Service\User\CurrentUser;

class UserProvider implements UserProviderInterface {

    public function __construct ($container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername ($username) {
        $user = $this->getUserService()->getUserByUsername($username);
        if (empty($user)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
        // $user['currentIp'] = $this->container->get('request')->getClientIp();
        $currentUser = new CurrentUser();
        return $currentUser->fromArray($user);
    }

    public function refreshUser (UserInterface $user) {
        if (! $user instanceof CurrentUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass ($class) {
        return $class === 'Acme\Service\User\CurrentUser';
    }

    protected function getUserService()
    {
        return ServiceKernel::instance()->createService('User.UserService');
    }

}