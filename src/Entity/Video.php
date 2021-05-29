<?php

namespace Svc\VideoBundle\Entity;

use Svc\VideoBundle\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 * @UniqueEntity(fields={"title"}, message="There is already a video with this title")
 */
class Video
{

  public const SOURCE_YOUTUBE = 0;
  public const SOURCE_VIMEO = 1;

  public const SOURCES_LIST = [
    'Youtube' => Video::SOURCE_YOUTUBE,
    'Vimeo' => Video::SOURCE_VIMEO,
  ];

  private const SOURCES_TEXT = [
    Video::SOURCE_YOUTUBE => 'Youtube',
    Video::SOURCE_VIMEO => 'Vimeo',
  ];

  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=100, unique=true)
   * @Assert\NotBlank
   */
  private $title;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  private $description;

  /**
   * @ORM\Column(type="string", length=8, unique=true, nullable=true)
   * @Assert\Regex(
   *     pattern     = "/^[a-z0-9_\-]+$/",
   *     message     = "Please use lowercase letters, numbers, minus and underscore only"
   * )
   * @Assert\Length(
   *      min = 4,
   *      max = 8,
   *      minMessage = "Your shortname must be at least {{ limit }} characters long",
   *      maxMessage = "Your shortname cannot be longer than {{ limit }} characters"
   * )
   */
  private $shortName;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $isPrivate = false;

  /**
   * @ORM\Column(type="string", length=30)
   * @Assert\NotBlank
   */
  private $sourceID;

  /**
   * @ORM\Column(type="smallint")
   */
  private $sourceType = Video::SOURCE_VIMEO;

  /**
   * @ORM\Column(type="string", length=20)
   * @Assert\NotBlank
   */
  private $ratio = '16x9';

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $subTitle;

  /**
   * @ORM\Column(type="integer")
   */
  private $likes = 0;

  /**
   * @ORM\Column(type="integer")
   */
  private $calls = 0;

  /**
   * @ORM\ManyToOne(targetEntity=VideoGroup::class, inversedBy="videos")
   * @ORM\JoinColumn(nullable=true)
   */
  private $videoGroup;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): self
  {
    $this->title = $title;

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

  public function getSourceID(): ?string
  {
    return $this->sourceID;
  }

  public function setSourceID(string $sourceID): self
  {
    $this->sourceID = $sourceID;

    return $this;
  }

  public function getSourceType(): ?int
  {
    return $this->sourceType;
  }

  public function setSourceType(int $sourceType): self
  {
    $this->sourceType = $sourceType;

    return $this;
  }

  public function getSourceText(): string
  {
    if ($this->sourceType !== null and array_key_exists($this->sourceType, Video::SOURCES_TEXT)) {
      return Video::SOURCES_TEXT[$this->sourceType];
    }
    return 'n/a';
  }

  public function getRatio(): ?string
  {
    return $this->ratio;
  }

  public function setRatio(string $ratio): self
  {
    $this->ratio = $ratio;

    return $this;
  }

  public function getSubTitle(): ?string
  {
    return $this->subTitle;
  }

  public function setSubTitle(?string $subTitle): self
  {
    $this->subTitle = $subTitle;

    return $this;
  }

  public function getLikes(): ?int
  {
    return $this->likes;
  }

  public function setLikes(int $likes): self
  {
    $this->likes = $likes;

    return $this;
  }

  public function incLikes(): int
  {
    $this->likes++;
    return $this->likes;
  }

  public function incCalls(): int
  {
    $this->calls++;
    return $this->calls;
  }

  public function getCalls(): ?int
  {
    return $this->calls;
  }

  public function setCalls(int $calls): self
  {
    $this->calls = $calls;

    return $this;
  }

  public function getShortName(): ?string
  {
    return $this->shortName;
  }

  public function setShortName(string $shortName): self
  {
    $this->shortName = $shortName;

    return $this;
  }

  public function getIsPrivate(): ?bool
  {
    return $this->isPrivate;
  }

  public function setIsPrivate(bool $isPrivate): self
  {
    $this->isPrivate = $isPrivate;

    return $this;
  }

  public function getVideoGroup(): ?VideoGroup
  {
    return $this->videoGroup;
  }

  public function setVideoGroup(?VideoGroup $videoGroup): self
  {
    $this->videoGroup = $videoGroup;

    return $this;
  }
}
