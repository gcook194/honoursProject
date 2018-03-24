<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/02/2018
 * Time: 21:04
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\FixtureEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Fixture;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;

class FixtureEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('event_type', ChoiceType::class, array(
            'required' => true,
            'placeholder' => 'Select One',
            'choices' => array(
                'Goal' => 'goal',
                'Assist' => 'assist',
                'Yellow Card' => 'yellow',
                'Red Card' => 'red',
            ),
        ));

        $formModifier = function (FormInterface $form, Fixture $fixture = null) {

            $homePlayers = $fixture->getHomeTeam()->getPlayers()->getValues();
            $awayPlayers = $fixture->getAwayTeam()->getPlayers()->getValues();

            $allPlayers = [];

            for ($i = 0; $i < count($homePlayers); $i++) {
                array_push($allPlayers, $homePlayers[$i]);
            }

            for ($i = 0; $i < count($awayPlayers); $i++) {
                array_push($allPlayers, $awayPlayers[$i]);
            }

            $form->add('player', EntityType::class, array(
                'class' => 'AppBundle:Player',
                'placeholder' => 'Select One',
                'choices' => $allPlayers,
                'required' => true,
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getFixture());
            }
        );

        /*$builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $fixture = $event->getForm()->getData()->getFixture();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm(), $fixture);
            }
        );*/

        $builder->add('timeDisplay', TextType::class, array(
            'mapped' => false,
        ));

        $builder->add('time', RangeType::class, array(
            'attr' => array(
                'min' => 1,
                'max' => 120
            )
        ));

        $builder->add('submit', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FixtureEvent::class,
        ));
    }
}