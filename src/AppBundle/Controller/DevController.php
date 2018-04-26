<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 13:31
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Category;
use AppBundle\Services\CodeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Services\DbService;
use AppBundle\Services\UserService;
use AppBundle\Services\EntityService;
use AppBundle\Services\RanckService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\EntityRepository;


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


        return 'truc';
    }



}