<?php

namespace Svc\VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\MappedSuperclass()
 */
class _VideoSuperclass
{
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
  protected $shortName;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $isPrivate = false;

  /**
   * helper type to store password in form, not stored in database
   *
   * @var string
   * @Assert\Expression(
   *     "this.getPlainPassword() or !this.getIsPrivate()",
   *     message="You have to define a password for private videos"
   * )   */
  private $plainPassword;

  /**
   * @var string The hashed password
   * @ORM\Column(type="string", nullable=true)
   */
  private $password;

  /**
   * @ORM\Column(type="boolean", options={"default": false})
   */
  private $hideOnHomePage = false;

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

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(?string $password = null): self
  {
    $this->password = $password;

    return $this;
  }

  public function getPlainPassword(): ?string
  {
    return $this->plainPassword;
  }

  public function setPlainPassword(?string $plainPassword = null): self
  {
    $this->plainPassword = $plainPassword;

    return $this;
  }

  public function getHideOnHomePage(): ?bool
  {
    return $this->hideOnHomePage;
  }

  public function setHideOnHomePage(bool $hideOnHomePage): self
  {
    $this->hideOnHomePage = $hideOnHomePage;

    return $this;
  }
}
