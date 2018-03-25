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

class RaceNewType extends AbstractType
{

    public function contactAction(Request $request)
    {
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        return $form;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }

        // ... render the form
    }

    
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
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (CategoryRepository $cr) use ($organizer) {
                    return $cr->categoriesByOrganizer($organizer);
                }
            ));
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
