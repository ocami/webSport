<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Competition;
use AppBundle\Entity\User;
use AppBundle\Entity\RaceCompetitor;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Services\ToolsService;


class EntityService
{
    private $ts;
    private $ac;
    private $em;
    private $cs;
    private $ci;
    private $tools;
    private $user;

    public function __construct(
        TokenStorageInterface $ts,
        AuthorizationCheckerInterface $ac,
        EntityManagerInterface $em,
        CodeService $cs,
        ContainerInterface $ci,
        ToolsService $tools

    )
    {
        $this->ts = $ts;
        $this->ac = $ac;
        $this->em = $em;
        $this->cs = $cs;
        $this->ci = $ci;
        $this->tools = $tools;
        $this->user = $this->ts->getToken()->getUser();
    }

    public function create($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        $this->cs->generateCode($entity);
    }

    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

}