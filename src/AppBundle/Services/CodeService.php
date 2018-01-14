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
use Doctrine\ORM\EntityManagerInterface;


class CodeService
{
    private $em;
    private $mg;

    public function __construct(EntityManagerInterface $em, MessageGenerator $mg)
    {
        $this->em = $em;
        $this->mg = $mg;
    }

    public function generate($entity)
    {
        $number = $this->lastCodeId($entity);

        switch (get_class($entity))
        {
            case Organizer::class :
                $code = 'ORGAN_'.$number;
                break;

            case Competitor::class :
                $code = 'CPTOR_'.$number;
                break;

            case Championship::class :
                $code = 'CSHIP_'.$number;
                break;

            case Competition::class :
                $code = 'COMPN_'.$this->codeFormat($entity->getOrganizer()->getId()).'_'.$number;
                break;

            case Race::class :
                $codeCompetition = $this->codeFormat($entity->getCompetition()->getId());

                if($entity->getChampionships()->isEmpty())
                    $code = 'RACEF_'.$codeCompetition.'_'.$number;
                else
                    $code = 'RACEC_'.$codeCompetition.'_'.$number;
                break;

            case Category::class :
                $code = 'CATEG_'.$number;
                break;
        }

        $entity->setCode($code);

        return $entity;
    }

    private function lastCodeId($class)
    {
        $class=get_class($class);

        $nbr =   $this->em->createQueryBuilder('a')
            ->select('MAX(e.id)')
            ->from($class, 'e')
            ->getQuery()
            ->getSingleScalarResult()+1;

        return $this->codeFormat($nbr);
    }

    private function codeFormat($nbr)
    {
        return sprintf( "%03d", $nbr );
    }
}