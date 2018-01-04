<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 14:03
 */

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUser implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {

        /*
        // Les noms d'utilisateurs à créer
        $listNames = array('toto', 'tata', 'tutu');

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User;

            // Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $user->setUsername($name);
            $user->setPassword($name);
            $user->setEmail($name.'@truc.fr');
            $user->setEnabled(true);

            // On ne se sert pas du sel pour l'instant
            $user->setSalt('');
            // On définit uniquement le role ROLE_USER qui est le role de base
            $user->setRoles(array('ROLE_USER'));

            // On le persiste
            $manager->persist($user);
        }

        // On déclenche l'enregistrement
        $manager->flush();
        */
    }
}