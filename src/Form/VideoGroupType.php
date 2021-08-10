<?php

namespace Svc\VideoBundle\Form;

use Svc\VideoBundle\Entity\VideoGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class VideoGroupType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('name', null, [
        'label' => 'Name',
        'attr' => ['autofocus' => 'autofocus'],
        'constraints' => [
          new Length([
            'min' => 3,
            'max' => 15,
          ]),
        ],
      ])
      ->add('description', null, [
        'label' => 'Description',
        'constraints' => [
          new Length([
            'min' => 6,
            'max' => 200,
          ]),
        ],
    ]);

    if ($options['enableShortNames']) {
      $builder
        ->add('shortName', TextType::class, [
          "help" => "The short name used in the link, please use lowercase letters, numbers, minus and underscore only",
          "attr" => ['pattern' => '[a-z0-9_\-]{4,8}', 'title' => 'Please use lowercase letters, numbers, minus and underscore only and between 4 and 8 chars']
        ]);
    }

    $builder
      ->add('hideNav', null, [
        'label' => 'Hide navigation',
        'label_attr' => ['class' => 'checkbox-switch']
      ])
      ->add('hideGroups', null, [
        'label_attr' => ['class' => 'checkbox-switch']
      ])
      ->add('hideOnHomePage', null, [
        'label_attr' => ['class' => 'checkbox-switch'],
        'help' => 'You should set "Hide groups" too.'
      ]);
      
    if ($options['enablePrivate']) {
      $builder
        ->add('isPrivate', null, [
          'help' => 'Is the video group private?',
          'label' => 'Private',
          'label_attr' => ['class' => 'checkbox-switch']
        ])
        ->add('plainPassword', PasswordType::class, [
          'label' => 'Password (only used for private video groups)',
          'help' => 'It should have between 6 and 12 characters',
          'required' => false,
          'constraints' => [
            new Length([
              'min' => 6,
              'max' => 12,
            ]),
          ],
          'attr' => ['data-svc--util-bundle--show-password-target' => "passwordFld"],
          'row_attr' => [
            'data-controller' => "svc--util-bundle--show-password",
            'data-svc--util-bundle--show-password-show-text-value' => "Show password",
            'data-svc--util-bundle--show-password-hide-text-value' => "Hide password"
          ]
        ]);
    }
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => VideoGroup::class,
      'translation_domain' => 'VideoBundle',
      'enableShortNames' => false,
      'enablePrivate' => true
    ]);
  }
}
