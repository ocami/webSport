<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Championship;
use AppBundle\Entity\ChampionshipCompetitor;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class DemoService
{
    private $ts;
    private $em;
    private $tools;
    private $user;
    private $message = '';

    public function __construct(
        TokenStorageInterface $ts,
        EntityManagerInterface $em,
        ToolsService $tools
    )
    {
        $this->ts = $ts;
        $this->em = $em;
        $this->tools = $tools;
        $this->user = $this->ts->getToken()->getUser();
    }





}