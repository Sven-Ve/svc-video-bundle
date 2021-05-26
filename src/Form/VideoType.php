<?php

namespace App\Form;

use App\Entity\Video;
use App\Service\VideoHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('title')
      ->add('subTitle', null, [
        'required' => false
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
      ->add('calls')
      ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Video::class,
    ]);
  }
}
