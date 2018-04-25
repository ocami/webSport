<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/01/2018
 * Time: 20:18
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Services\UserService;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class DateService
{
    public function format($date,$format)
    {
        $d = new \DateTime($date);
        $d = $d->format($format);

        return $d;
    }
}