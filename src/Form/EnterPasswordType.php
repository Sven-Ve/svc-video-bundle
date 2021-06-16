<?php

namespace Svc\VideoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnterPasswordType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('plainPassword', PasswordType::class, [
        'label' => 'Password',
        'mapped' => false,
        'attr' => ['autofocus' => true, 'placeholder' => 'Password'],
        'row_attr' => ['class' => 'form-floating']
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => null,
      'translation_domain' => 'VideoBundle'
    ]);
  }
}
