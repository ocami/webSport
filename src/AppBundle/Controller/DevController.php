<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DevController extends Controller
{

    /**
     * @Route("/dev/remove_competitions", name="dev_remove_competitions")
     */
    public function removeAllCompetitions()
    {
        $conn = $this->getDoctrine()->getConnection();

        $sql = 'DELETE FROM race_category;
DELETE FROM race_championship;
DELETE FROM championship_competitor;
DELETE FROM race_competitor;
DELETE FROM race;
DELETE FROM competition;
DELETE FROM location;

ALTER TABLE race_category AUTO_INCREMENT=0;
ALTER TABLE race_championship AUTO_INCREMENT=0;
ALTER TABLE championship_competitor AUTO_INCREMENT=0;
ALTER TABLE race AUTO_INCREMENT=0;
ALTER TABLE race_competitor AUTO_INCREMENT=0;
ALTER TABLE location AUTO_INCREMENT=0;
ALTER TABLE competition AUTO_INCREMENT=0;
';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        var_dump($stmt->fetchAll());die;


        return 'all remmove';
    }

    /**
     * @Route("/becomeAdmin", name="becomeAdmin")
     */
    public function becomeAdmin()
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $this->getUser();

        $user->addRole('ROLE_ADMIN');

        $this->get(UserService::class)->refreshToken();

        $userManager->updateUser($user);

        return $this->render('home/test.html.twig', array(
            'message' => "Role admin",
        ));

    }

}