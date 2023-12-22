<?php

namespace Svc\VideoBundle\Form;

use Svc\VideoBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class TagType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', null, [
        'label' => 'Name',
        'attr' => ['autofocus' => 'autofocus'],
        'constraints' => [
          new Length([
            'min' => 3,
            'max' => 50,
          ]),
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Tag::class,
      'translation_domain' => 'VideoBundle',
    ]);
  }
}
