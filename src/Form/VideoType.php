<?php

namespace Svc\VideoBundle\Form;

use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class VideoType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('title', null,  ["attr" => ["autofocus" => true]])
      ->add('subTitle', null, [
        'required' => false,
      ])
      ->add('videoGroup', null, [
        'label' => 'Group'
      ]);

    if ($options['enableShortNames']) {
      $builder
        ->add('shortName', TextType::class, [
          "help" => "The short name used in the link, please use lowercase letters, numbers, minus and underscore only",
          "attr" => ['pattern' => '[a-z0-9_\-]{4,8}', 'title' => 'Please use lowercase letters, numbers, minus and underscore only and between 4 and 8 chars']
        ]);
    }

    $builder
      ->add('isPrivate', null, [
        'help' => 'Is the video private?',
        'label' => 'Private',
        'label_attr' => [ 'class' => 'checkbox-switch']
      ])
      ->add('plainPassword', TextType::class, [
        'label' => 'Password (only used for private videos)',
        'help' => 'Your password should be at least 6 characters',
//        'mapped' => false,
        'required' => false,
        'data' => $options['plainPassword'],
        'constraints' => [
          new Length([
            'min' => 6,
            'minMessage' => 'Your password should be at least {{ limit }} characters',
            'max' => 12,
          ]),
        ]
      ])
      ->add('description', TextareaType::class, [
        'attr' => [
          'data-controller' => 'wysiwyg'
        ],
        'required' => false
      ])
      ->add('sourceID', null, [
        'label' => 'Source ID'
      ])
      ->add('sourceType', ChoiceType::class, [
        'choices' => Video::SOURCES_LIST,
        'label' => 'Source'
      ])
      ->add('ratio', ChoiceType::class, [
        'choices' => VideoHelper::getRatioList(),
        'placeholder' => 'Choose an ratio',
        'choice_label' => function ($choice, $key, $value) {
          return $value;
        }
      ])
      ->add('likes')
      ->add('calls');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Video::class,
      'translation_domain' => 'VideoBundle',
      'enableShortNames' => false,
      'plainPassword' => null,
    ]);
  }
}
