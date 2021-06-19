<?php

namespace Svc\VideoBundle\Entity;

use Svc\VideoBundle\Repository\VideoGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @ORM\Entity(repositoryClass=VideoGroupRepository::class)
 * @UniqueEntity(fields={"name"}, message="There is already a video group with this name")
 */
class VideoGroup extends _VideoSuperclass
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=40, unique=true)
   * @Assert\NotBlank
   */
  private $name;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $description;

  /**
   * @ORM\OneToMany(targetEntity=Video::class, mappedBy="videoGroup")
   */
  private $videos;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $defaultGroup = false;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $hideNav = false;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $hideGroups = false;


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

  public function __toString()
  {
    return $this->name;
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

}
