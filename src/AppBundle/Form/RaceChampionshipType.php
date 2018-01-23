<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RaceChampionshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('categories')//->add('categories')
            ->add('championships', EntityType::class, array(
                'class'        => 'AppBundle:Championship',
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true
            ))
        ;
    }


    public function getParent()
    {
        return RaceType::class;
    }
}
