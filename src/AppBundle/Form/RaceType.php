<?php

namespace AppBundle\Form;

use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Services\MessageGenerator;
use Symfony\Component\Validator\Constraints\Collection;

class RaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $organizer = $options['organizer'];

        $builder
            ->add('name')
            ->add('km')
            ->add('date')
            ->add('time')
            ->add('categories', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true,
                'query_builder' => function(CategoryRepository $cr) use($organizer) {
                    return $cr->categoriesByOrganizer($organizer);
                }
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Race',
            'organizer' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_race';
    }


}
