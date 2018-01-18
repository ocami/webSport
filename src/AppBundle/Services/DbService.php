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


class DbService
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

    public function generateUser()
    {
        $this->generateAdmin();
        $this->generateCompetitors();
        $this->generateOrganizers();
    }

    public function genreateCategories()
    {
        $json = file_get_contents("../src/AppBundle/categories.json");
        $datas = json_decode($json, true);

        $i=0;
        foreach ($datas as $data) {

            $i++;
            $category = new Category();
            $category->setCode('CATEG_'.$this->cs->codeFormat($i));
            $category->setName($data['name']);
            $category->setSexe($data['sexe']);
            $category->setAgeMax($data['ageMax']);
            $category->setAgeMin($data['ageMin']);
            $category->setCreateBy($data['createBy']);
            $this->em->persist($category);
        }
        $this->em->flush();
    }

    public function generateRaces()
    {
        $json = file_get_contents("../src/AppBundle/competition.json");
        $datas = json_decode($json, true);

        $i=0;
        foreach ($datas as $data) {
            $i++;
            $date =$this->tools->randomDate('01-01-2018','31-12-2018');

            $competition = new Competition();
            $competition->setCode('COMPN_'.$i);
            $competition->setName($data['name']);
            $competition->setDescription($data['text']);
            $competition->setVille($data['ville']);
            $competition->setDep($data['dep']);
            $competition->setAdress($data['adress']);
            $competition->setDateStart($date);
            $competition->setDateEnd($date);
            $competition->setOrganizer($this->em->getRepository(Organizer::class)->find($i+1));
            $this->em->persist($competition);
        }
        $this->em->flush();
    }

    private function generateAdmin()
    {
        $userManager = $this->ci->get('fos_user.user_manager');
        $userAdmin = $userManager->createUser();
        $userAdmin->setUsername('admin');
        $userAdmin->setEmail('admin@admin.fr');
        $userAdmin->setPlainPassword('001');
        $userAdmin->setEnabled(true);
        $userAdmin->addRole('ROLE_ADMIN');
        $userManager->updateUser($userAdmin, true);

        $competitor = new Competitor();
        $competitor->setFirstName('Admin');
        $competitor->setLastName('Admin');
        $competitor->setDate(new \DateTime('14-03-1983'));
        $competitor->setSexe('m');
        $competitor->setUserId($userAdmin->getId());
        $this->em->persist($competitor);
        $this->em->flush();
        $this->cs->generateCode($competitor);

        $organizer = new Organizer();
        $organizer->setName('Admin');
        $organizer->setUserId($userAdmin->getId());
        $this->em->persist($organizer);
        $this->em->flush();
        $this->cs->generateCode($organizer);
    }

    private function generateCompetitors()
    {
        $userManager = $this->ci->get('fos_user.user_manager');
        $json = file_get_contents("../src/AppBundle/name.json");
        $datas = json_decode($json, true);

        $i=1;
        foreach ($datas as $data)
        {
            $i++;
            if($i==91)
                break;
            $competitor = new Competitor();
            $user=new User();

            $user->setUsername($data['LastName']);
            $user->setEmail($data['LastName'].'@mail.com');
            $user->setPlainPassword('001');
            $user->setEnabled(true);
            $user->addRole('ROLE_COMPETITOR');
            $userManager->updateUser($user, true);

            $competitor->setFirstName($data['FirstName']);
            $competitor->setLastName($data['LastName']);
            $competitor->setDate($this->tools->randomDate());
            $competitor->setSexe($data['sexe']);
            $competitor->setUserId($i);
            $competitor->setCode('CPTOR_'.$this->cs->codeFormat($i));

            $this->em->persist($user);
            $this->em->persist($competitor);
        }
        $this->em->flush();
    }

    private function generateOrganizers()
    {
        $userManager = $this->ci->get('fos_user.user_manager');
        $json = file_get_contents("../src/AppBundle/organizer.json");
        $datas = json_decode($json, true);

        $i=0;
        foreach ($datas as $data)
        {
            $i++;
            $organizer = new Organizer();
            $user=new User();

            $user->setUsername('organizer'.$i);
            $user->setEmail('organizer'.$i.'@mail.com');
            $user->setPlainPassword('001');
            $user->setEnabled(true);
            $user->addRole('ROLE_ORGANIZER');
            $userManager->updateUser($user, true);

            $organizer->setName($data['name']);
            $organizer->setUserId($user->getId());


            $this->em->persist($organizer);
            $this->em->flush();
            $this->cs->generateCode($organizer);
        }
    }


    public function simulateRace($race)
    {
        $raceCompetitors = $this->em->getRepository(RaceCompetitor::class)->findByRace($race);

        foreach ($raceCompetitors as $rc) {
            $rc->setChrono(new \DateTime('1:22:30'));
            $rc->setChrono($this->tools->randomDate('2:00:00','3:30:00','H:i:s'));
            $this->em->persist($rc);
        }

        $this->em->flush();
    }

    public function simulateRegistration($race)
    {
        $competitors = $this->em->getRepository(Competitor::class)->findAll();
        $i = 0;

        foreach ($competitors as $competitor) {
            $i++;
            $competitorYear = $competitor->getDate()->format('Y');

            foreach ($race->getCategories() as $category) {

                if ($category->getSexe() == 'mx' OR $category->getSexe() == $competitor->getSexe()) {
                    if ($competitorYear <= $category->getAgeMin() AND $competitorYear >= $category->getAgeMax()) {
                        $raceCompetitor = new RaceCompetitor();
                        $raceCompetitor->setRace($race);
                        $raceCompetitor->setCompetitor($competitor);
                        $raceCompetitor->setNumber($i);
                        $this->em->persist($raceCompetitor);
                        break;
                    }
                }
            }
        }
        $this->em->flush();
        return 'registration ok';
    }
}