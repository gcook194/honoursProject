<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/01/2018
 * Time: 21:38
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Team;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class TeamUpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder->add('name');
        $builder->add('twitter_url', null, array('required' => false));
        $builder->add('website_url', null, array('required' => false));
        $builder->add('nickname');
        $builder->add('display_name');
        $builder->add('id', HiddenType::class, array(
            'data' => $entity->getId(),
        ));
        $builder->add('badgeImage', FileType::class, array('label' => 'Badge Image (100x100 PNG)', 'required' => false));
        $builder->add('save', SubmitType::class, array(
            'attr' => array('class' => 'btn btn-primary'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Team::class,
        ));
    }
}