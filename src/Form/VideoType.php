<?php

namespace Svc\VideoBundle\Form;

use Svc\VideoBundle\Entity\Video;
use Svc\VideoBundle\Form\Type\TagInputType;
use Svc\VideoBundle\Service\VideoHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
      ->add('title', null, ['attr' => ['autofocus' => true]])
      ->add('subTitle', null, [
        'required' => false,
      ]);

    if ($options['enableGroups']) {
      $builder
        ->add('videoGroup', null, [
          'label' => 'Group',
        ])
      ;
    }

    if ($options['enableShortNames']) {
      $builder
        ->add('shortName', TextType::class, [
          'help' => 'The short name used in the link, please use lowercase letters, numbers, minus and underscore only',
          'attr' => ['pattern' => '[a-z0-9_\-]{4,8}', 'title' => 'Please use lowercase letters, numbers, minus and underscore only and between 4 and 8 chars'],
        ])
      ;
    }

    if ($options['enableTagging']) {
      $builder
        ->add('tags', TagInputType::class, [
          'required' => false,
          'help' => 'A maximum of 4 tags are allowed',
        ]);
    }

    $builder
      ->add('hideOnHomePage', null, [
        'label_attr' => ['class' => 'checkbox-switch'],
        'help' => "Should video be displayed at 'All Videos'?",
      ]);

    if ($options['enablePrivate']) {
      $builder
        ->add('isPrivate', null, [
          'help' => 'Is the video private?',
          'label' => 'Private',
          'label_attr' => ['class' => 'checkbox-switch'],
        ])
        ->add('plainPassword', PasswordType::class, [
          'label' => 'Password (only used for private videos)',
          'help' => 'It should have between 6 and 12 characters',
          'required' => false,
          'constraints' => [
            new Length([
              'min' => 6,
              'max' => 12,
            ]),
          ],
          'attr' => ['data-svc--util-bundle--show-password-target' => 'passwordFld'],
          'row_attr' => [
            'data-controller' => 'svc--util-bundle--show-password',
            'data-svc--util-bundle--show-password-show-text-value' => 'Show password',
            'data-svc--util-bundle--show-password-hide-text-value' => 'Hide password',
          ],
        ])
      ;
    }

    $builder
      ->add('description', TextareaType::class, [
        'attr' => [
          'data-controller' => 'svc--util-bundle--wysiwyg',
        ],
        'required' => false,
      ])
      ->add('sourceID', null, [
        'label' => 'Source ID',
      ])
      ->add('sourceType', ChoiceType::class, [
        'choices' => Video::SOURCES_LIST,
        'label' => 'Source',
      ])
      ->add('ratio', ChoiceType::class, [
        'choices' => VideoHelper::getRatioList(),
        'placeholder' => 'Choose an ratio',
        'choice_label' => fn ($choice, $key, $value) => $value,
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
      'enablePrivate' => true,
      'enableGroups' => true,
      'enableTagging' => false,
    ]);
  }
}
