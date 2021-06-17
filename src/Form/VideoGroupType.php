<?php

namespace Svc\VideoBundle\Form;

use Svc\VideoBundle\Entity\VideoGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoGroupType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name')
      ->add('description')
      ->add('hideNav', null, [
        'label'=>'Hide navigation',
        'label_attr' => [ 'class' => 'checkbox-switch']
      ])
      ->add('hideGroups', null, [
        'label_attr' => [ 'class' => 'checkbox-switch']
      ])
      ->add('hideOnHomePage', null, [
        'label_attr' => [ 'class' => 'checkbox-switch'],
        'help' => 'You should set "Hide groups" too.'
      ])
      ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => VideoGroup::class,
      'translation_domain' => 'VideoBundle'
    ]);
  }
}