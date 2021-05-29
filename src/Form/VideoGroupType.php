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
      ->add('description');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => VideoGroup::class,
      'translation_domain' => 'VideoBundle'
    ]);
  }
}