<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/01/2018
 * Time: 17:05
 */

namespace AppBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{
    private $ts;
    private $em;
    private $cs;

    public function __construct(
        TokenStorageInterface $ts,
        EntityManagerInterface $em,
        CodeService $cs
    )
    {
        $this->ts = $ts;
        $this->em = $em;
        $this->cs = $cs;
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