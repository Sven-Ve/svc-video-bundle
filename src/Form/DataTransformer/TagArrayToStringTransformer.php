<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\VideoBundle\Form\DataTransformer;

use Svc\VideoBundle\Entity\Tag;
use Svc\VideoBundle\Repository\TagRepository;
use Symfony\Component\Form\DataTransformerInterface;

use function Symfony\Component\String\u;

class TagArrayToStringTransformer implements DataTransformerInterface
{
  public function __construct(
    private readonly TagRepository $tags
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value): string
  {
    // The value received is an array of Tag objects generated with
    // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
    // The value returned is a string that concatenates the string representation of those objects

    /* @var Tag[] $tags */
    return implode(',', $value);
  }

  /**
   * {@inheritdoc}
   */
  public function reverseTransform($value): array
  {
    if (null === $value || u($value)->isEmpty()) {
      return [];
    }

    // @phpstan-ignore-next-line
    $names = array_filter(array_unique(array_map('trim', u($value)->split(','))));

    $tags = $this->tags->findBy([
      'name' => $names,
    ]);
    $newNames = array_diff($names, $tags);

    foreach ($newNames as $name) {
      $tag = new Tag($name);
      $tags[] = $tag;
    }

    return $tags;
  }
}
