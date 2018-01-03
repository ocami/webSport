<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class UserService
{

    public function refreshToken($user)
    {

        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        return $token;
    }
}