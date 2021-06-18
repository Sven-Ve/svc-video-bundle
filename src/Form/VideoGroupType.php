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
      ->add('name', null, [
        'label' => 'Name',
        'attr' => ['autofocus' => true, 'placeholder' => 'Name'],
        'row_attr' => ['class' => 'form-floating mb-3'],
//        'help' => 'Name of this video group'
      ])
      ->add('description', null, [
        'label' => 'Description',
        'attr' => ['placeholder' => 'Description'],
        'row_attr' => ['class' => 'form-floating mb-3']
      ])
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