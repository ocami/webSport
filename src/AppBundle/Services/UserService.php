<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;


class UserService
{
    private $ts;
    private $ac;
    private $em;
    private $cs;
    private $user;

    public function __construct(TokenStorageInterface $ts, AuthorizationCheckerInterface $ac, EntityManagerInterface $em, CodeService $cs)
    {
        $this->ts = $ts;
        $this->ac = $ac;
        $this->em = $em;
        $this->cs = $cs;
        $this->user = $this->ts->getToken()->getUser();
    }

    public function refreshToken()
    {
        $token = new UsernamePasswordToken(
            $this->user,
            null,
            'main',
            $this->user->getRoles()
        );
        return $token;
    }

    public function isOrganizerComeptition($competition)
    {
        $isOrganizer = false;

        if ($this->ac->isGranted('ROLE_ORGANIZER'))
            if ($this->user->getId() == $competition->getOrganizer()->getUserId())
                $isOrganizer = true;

        return $isOrganizer;
    }

    public function isOrganizerRace($race)
    {
        return $this->isOrganizerComeptition($race->getCompetition());
    }

    public function registerUserApp($userApp)
    {
        switch (get_class($userApp)) {
            case Competitor::class :
                $this->user->addRole('ROLE_COMPETITOR');
                break;

            case Organizer::class :
                $this->user->addRole('ROLE_ORGANIZER');
                break;
        }
        $userApp->setUserId($this->user->getId());


        $this->em->persist($userApp);
        $this->em->persist($this->user);
        $this->em->flush();
        $this->cs->generate($userApp);

        $token = $this->refreshToken();
        $this->ts->setToken($token);
    }

    public function currentUserApp($userApp)
    {
        switch ($userApp) {
            case Competitor::class :
                return $this->em->getRepository(Competitor::class)->findOneByUserId($this->user);

            case Organizer::class :
                return $this->em->getRepository(Organizer::class)->findOneByUserId($this->user);

            default :
                return new InvalidArgumentException('UserService/cureentUserApp function accept only Competitor or Organizer class');
        }
    }

}