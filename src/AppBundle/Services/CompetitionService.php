<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/01/2018
 * Time: 08:21
 */

namespace AppBundle\Services;

use AppBundle\Entity\Competitor;
use AppBundle\Entity\Category;
use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CompetitionService
{
    private $em;
    private $us;
    private $cs;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }




}