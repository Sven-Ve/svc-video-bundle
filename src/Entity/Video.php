<?php

namespace Svc\VideoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Svc\VideoBundle\Enum\SourceType;
use Svc\VideoBundle\Repository\VideoRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'There is already a video with this title')]
class Video extends _VideoSuperclass
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column()]
  private ?int $id = null;

  #[ORM\Column(length: 100, unique: true)]
  #[Assert\NotBlank]
  private ?string $title = null;

  #[ORM\Column(type: 'text', nullable: true)]
  private ?string $description = null;

  #[ORM\Column(type: 'string', length: 30)]
  #[Assert\NotBlank]
  private ?string $sourceID = null;

  #[ORM\Column(type: 'smallint', enumType: SourceType::class)]
  private SourceType $sourceType = SourceType::VIMEO;

  #[ORM\Column(length: 20)]
  #[Assert\NotBlank]
  private string $ratio = '16x9';

  #[ORM\Column(nullable: true)]
  private ?string $subTitle = null;

  #[ORM\Column()]
  private int $likes = 0;

  #[ORM\Column()]
  private int $calls = 0;

  #[ORM\Column(nullable: true)]
  private ?string $thumbnailUrl = null;

  #[ORM\Column(nullable: true)]
  private ?string $thumbnailPath = null;

  #[ORM\ManyToOne(inversedBy: 'videos')]
  #[ORM\JoinColumn(nullable: true)]
  private ?VideoGroup $videoGroup = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTime $uploadDate = null;

  /**
   * @var Tag[]|Collection
   */
  #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'videos', cascade: ['persist'])]
  #[ORM\OrderBy(['name' => 'ASC'])]
  #[Assert\Count(max: 4, maxMessage: 'post.too_many_tags')]
  private Collection $tags;

  public function __construct()
  {
    $this->tags = new ArrayCollection();
  }

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

  public function getSourceType(): SourceType
  {
    return $this->sourceType;
  }

  public function setSourceType(SourceType $sourceType): self
  {
    $this->sourceType = $sourceType;

    return $this;
  }

  public function getSourceText(): string
  {
    return $this->sourceType->label();
  }

  public function getRatio(): string
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

  public function getThumbnailUrl(): ?string
  {
    return $this->thumbnailUrl;
  }

  public function isThumbnailUrl(): bool
  {
    return $this->thumbnailUrl !== null;
  }

  public function setThumbnailUrl(?string $thumbnailUrl): self
  {
    $this->thumbnailUrl = $thumbnailUrl;

    return $this;
  }

  public function getThumbnailPath(): ?string
  {
    return $this->thumbnailPath;
  }

  public function isThumbnailPath(): bool
  {
    return $this->thumbnailPath !== null;
  }

  public function setThumbnailPath(?string $thumbnailPath): self
  {
    $this->thumbnailPath = $thumbnailPath;

    return $this;
  }

  public function getLikes(): int
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
    ++$this->likes;

    return $this->likes;
  }

  public function incCalls(): int
  {
    ++$this->calls;

    return $this->calls;
  }

  public function getCalls(): int
  {
    return $this->calls;
  }

  public function setCalls(int $calls): self
  {
    $this->calls = $calls;

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

  public function getUploadDate(): ?\DateTimeInterface
  {
    return $this->uploadDate;
  }

  public function setUploadDate(?\DateTimeInterface $uploadDate): self
  {
    $this->uploadDate = \DateTime::createFromInterface($uploadDate);

    return $this;
  }

  public function addTag(Tag $tag): self
  {
    $this->tags[] = $tag;

    return $this;
  }

  public function removeTag(Tag $tag): void
  {
    $this->tags->removeElement($tag);
  }

  public function getTags(): Collection
  {
    return $this->tags;
  }
}
