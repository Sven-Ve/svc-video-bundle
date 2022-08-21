<?php

namespace Svc\VideoBundle\Form\Type;

use Svc\VideoBundle\Form\DataTransformer\TagArrayToStringTransformer;
use Svc\VideoBundle\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TagInputType extends AbstractType
{
  public function __construct(private readonly TagRepository $tagRep)
  {
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->addModelTransformer(new CollectionToArrayTransformer(), true)
      ->addModelTransformer(new TagArrayToStringTransformer($this->tagRep), true);
  }

  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $json = $this->tagRep->getTagNamesAsJson();
    $view->vars['row_attr']['data-svc--video-bundle--tag-auto-com-list-value'] = $json;
    $view->vars['row_attr']['data-controller'] = 'svc--video-bundle--tag';
    $view->vars['attr']['data-svc--video-bundle--tag-target'] = 'tags';
  }

  public function getParent(): ?string
  {
    return TextType::class;
  }
}
