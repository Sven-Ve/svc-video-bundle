<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Svc\VideoBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Svc\VideoBundle\Repository\TagRepository;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag implements \JsonSerializable, \Stringable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 50, unique: true)]
  private ?string $name = null;

  #[ORM\ManyToMany(targetEntity: Video::class, mappedBy: 'tags')]
  protected Collection $videos;

  public function __construct(string $name = null)
  {
    if ($name !== null) {
      $this->setName($name);
    }
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setName(string $name): void
  {
    $this->name = u($name)->lower()->truncate(50);
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function __toString(): string
  {
    return $this->name;
  }

  public function addVideos(Video $video): self
  {
    $this->videos[] = $video;

    return $this;
  }

  public function removeVideo(Video $video): void
  {
    $this->videos->removeElement($video);
  }

  public function getVideos(): Collection
  {
    return $this->videos;
  }

  public function jsonSerialize(): string
  {
    return $this->name;
  }
}
