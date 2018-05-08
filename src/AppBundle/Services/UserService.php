<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Organizer;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCompetitor;
use AppBundle\Entity\User;
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
    private $cts;
    private $user;
    private $competitor;
    private $organizer;

    public function __construct(TokenStorageInterface $ts,
                                AuthorizationCheckerInterface $ac,
                                EntityManagerInterface $em,
                                CategoryService $cts,
                                CodeService $cs)
    {
        $this->ts = $ts;
        $this->ac = $ac;
        $this->em = $em;
        $this->cts = $cts;
        $this->cs = $cs;
        $this->user = $this->ts->getToken()->getUser();

        if ($this->ac->isGranted('ROLE_ORGANIZER'))
            $this->organizer = $this->em->getRepository(Organizer::class)->findOneByUserId($this->user);

        if ($this->ac->isGranted('ROLE_COMPETITOR'))
            $this->competitor = $this->em->getRepository(Competitor::class)->findOneByUserId($this->user);
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

    public function isOrganizerCompetition($competition)
    {
        $isOrganizer = false;

        if ($this->ac->isGranted('ROLE_ORGANIZER'))
            if ($this->user->getId() == $competition->getOrganizer()->getUserId())
                $isOrganizer = true;

        return $isOrganizer;
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

    /**
     * @param Competitor $competitor
     * @return Category
     */
    public function getCategoryCompetitor()
    {
        $competitorYear = $this->competitor->getDateObject()->format('Y');
        $gender = $this->competitor->getSexe();

        $category = $this->cts->getCategory($competitorYear,$gender);

        return $category;

       /* $categories = $this->em->getRepository(Category::class)->findAll();

        foreach ($categories as $category) {

            if ($competitorYear < $category->getAgeMin()
                && $competitorYear > $category->getAgeMax()
                && $category->getSexe() == $this->competitor->getSexe()
            )
                return $category;
        }
        return null;*/
    }

    public function addUserDataInRaces($races)
    {
        if($this->user == 'anon.')
            return $races;

        foreach ($races as $race) {
            $this->addUserDataInRace($race);
        }
        return $races;
    }

    public function addUserDataInRace(Race $race){
        if($this->user == 'anon.')
            return $race;

        if (!is_null($this->organizer))
            if ($this->isOrganizerCompetition($race->getCompetition()))
                $race->setIsOrganizer(true);

        if (!is_null($this->competitor))
            $race->setCompetitorRegister($this->RaceRegisterStatus($race));

        return $race;
    }

    public function addUserDataInCompetitions($competitions)
    {
        if($this->user == 'anon.')
            return $competitions;

        foreach ($competitions as $competition) {
            if (!is_null($this->organizer))
                if ($this->isOrganizerCompetition($competition))
                    $competition->setIsOrganizer(true);

            if (!is_null($this->competitor))
                $competition->setCompetitorRegister($this->CompetitionRegisterStatus($competition));
        }
        return $competitions;
    }

    public function RaceRegisterStatus(Race $race)
    {
        if ($this->em->getRepository(RaceCompetitor::class)->competitorIsRegisterToRace($race, $this->competitor))
            return 2;

        if ($race->getCategories()->contains($this->getCategoryCompetitor()))
            return 1;

        return 0;
    }

    public function CompetitionRegisterStatus(Competition $competition){

        $r = 0;
        foreach ($competition->getRaces() as $race){

            $s = $this->RaceRegisterStatus($race);

            if($s==2)
                return 2;

            if($s ==1)
                $r = 1;
        }

        return $r;
    }

    /**
     * @return Competitor
     */
    public function getCompetitor()
    {
        $competitor = $this->competitor;
        $competitor->setCategory($this->getCategoryCompetitor());
        return $competitor;
    }

    /**
     * @return Organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }



    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    public function currentUserApp($userApp)
//    {
//        switch ($userApp) {
//            case Competitor::class :
//                return $this->em->getRepository(Competitor::class)->findOneByUserId($this->user);
//
//            case Organizer::class :
//                return $this->em->getRepository(Organizer::class)->findOneByUserId($this->user);
//
//            default :
//                return new InvalidArgumentException('UserService/cureentUserApp function accept only Competitor or Organizer class');
//        }
//    }


}