<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/02/2018
 * Time: 15:33
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Player;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('number')
            ->add('position', ChoiceType::class, array(
                'choices' => array(
                    'Select One' => null,
                    'Goalkeeper' => 'gk',
                    'Defender' => 'def',
                    'Midfielder' => 'mid',
                    'Attacker' => 'att',
                    'All Rounder' => 'all',
                ),
            ))
            ->add('save', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Player::class,
        ));
    }
}