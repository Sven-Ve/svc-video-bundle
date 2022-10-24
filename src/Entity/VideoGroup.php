<?php

namespace Svc\VideoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Svc\VideoBundle\Repository\VideoGroupRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoGroupRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'There is already a video group with this name')]
class VideoGroup extends _VideoSuperclass implements \Stringable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column()]
  private ?int $id = null;

  #[ORM\Column(length: 40, unique: true)]
  #[Assert\NotBlank]
  private ?string $name = null;

  #[ORM\Column(nullable: true)]
  private ?string $description = null;

  #[ORM\OneToMany(mappedBy: 'videoGroup', targetEntity: Video::class)]
  private Collection $videos;

  #[ORM\Column(options: ['default' => false])]
  private bool $defaultGroup = false;

  #[ORM\Column(options: ['default' => false])]
  private bool $hideNav = false;

  #[ORM\Column(options: ['default' => false])]
  private bool $hideGroups = false;

  public function __construct()
  {
    $this->videos = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return Collection|Video[]
   */
  public function getVideos(): Collection
  {
    return $this->videos;
  }

  public function __toString(): string
  {
    return (string) $this->name;
  }

  public function getDefaultGroup(): ?bool
  {
    return $this->defaultGroup;
  }

  public function setDefaultGroup(bool $defaultGroup): self
  {
    $this->defaultGroup = $defaultGroup;

    return $this;
  }

  public function getHideNav(): ?bool
  {
    return $this->hideNav;
  }

  public function setHideNav(bool $hideNav): self
  {
    $this->hideNav = $hideNav;

    return $this;
  }

  public function getHideGroups(): ?bool
  {
    return $this->hideGroups;
  }

  public function setHideGroups(bool $hideGroups): self
  {
    $this->hideGroups = $hideGroups;

    return $this;
  }

  /**
   * needed for statistics, we can use the same name as for videos.
   */
  public function getTitle(): ?string
  {
    return $this->name;
  }
}
