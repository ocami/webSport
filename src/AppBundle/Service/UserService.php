<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class User
{

    protected $container;

    private function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function refreshToken($user)
    {
        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $security = $this->container->get('security.token_storage');

        $security->setToken($token);
    }
}