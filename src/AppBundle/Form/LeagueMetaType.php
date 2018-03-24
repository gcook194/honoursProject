<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\LeagueMeta;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class LeagueMetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('win_points', IntegerType::class, array('label' => 'Points per win'));
        $builder->add('draw_points', IntegerType::class, array('label' => 'Points per draw'));
        $builder->add('number_of_teams', IntegerType::class, array('label' => 'Number of teams'));
        $builder->add('sort_col_one', ChoiceType::class, array(
            'choices' => array(
                'Select One' => null,
                'Goal Difference' => 'goalDifference',
                'Goals Scored' => 'leagueUser',
            ),
            'label' => 'Table sort column 1',
        ));
        $builder->add('sort_col_two', ChoiceType::class, array(
            'choices' => array(
                'Select One' => null,
                'Goal Difference' => 'goalDifference',
                'Goals Scored' => 'leagueUser',
            ),
            'label' => 'Table sort column 2',
        ));
        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LeagueMeta::class,
        ));
    }
}