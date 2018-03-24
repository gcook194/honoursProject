<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/01/2018
 * Time: 16:16
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Fixture;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class FixtureUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('scheduledDate', DateType::class, array(
            'widget' => 'choice',
        ));
        $builder->add('playedDate', DateType::class, array(
            'widget' => 'choice',
        ));
        $builder->add('home_goals', IntegerType::class, array('label' => 'Home Goals', 'attr' => array('min' => 0)));
        $builder->add('away_goals', IntegerType::class, array('label' => 'Away Goals', 'attr' => array('min' => 0)));
        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Fixture::class,
        ));
    }
}