<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;




class CompetitionDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('code')
            ->remove('name')
            ->remove('ville')
            ->remove('adress')
            ->remove('dep')
            ->remove('dateStart')
            ->remove('dateEnd')
            ->add('description',TextareaType::class)

        ;
    }

    public function getParent()
    {
        return CompetitionType::class;
    }
}
