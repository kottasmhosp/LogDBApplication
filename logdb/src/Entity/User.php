<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(schema="public")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface {

  /**
   * @ORM\Id()
   * @ORM\GeneratedValue()
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $username;

  /**
   * @ORM\Column(type="string", length=500)
   */
  private $password;

  /**
   * @ORM\Column(type="boolean")
   */
  private $isActive;

  /**
   * @ORM\OneToMany(targetEntity="App\Entity\Actions", mappedBy="userId")
   */
  private $actions;


  public function __construct($username = NULL) {
    $this->isActive = TRUE;
    $this->username = $username;
    $this->actions = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getUsername(): ?string {
    return $this->username;
  }

  public function setUsername(string $username): self {
    $this->username = $username;

    return $this;
  }

  public function getPassword(): ?string {
    return $this->password;
  }

  public function setPassword(string $password): self {
    $this->password = $password;

    return $this;
  }

  public function getIsActive(): ?bool {
    return $this->isActive;
  }

  public function setIsActive(bool $isActive): self {
    $this->isActive = $isActive;

    return $this;
  }

  public function getRoles() {
    return array('ROLE_USER');
  }

  public function getSalt() {
    return null;
  }


  public function eraseCredentials() {
  }

  /**
   * @return Collection|Actions[]
   */
  public function getActions(): Collection
  {
      return $this->actions;
  }

  public function addAction(Actions $action): self
  {
      if (!$this->actions->contains($action)) {
          $this->actions[] = $action;
          $action->setUserId($this);
      }

      return $this;
  }

  public function removeAction(Actions $action): self
  {
      if ($this->actions->contains($action)) {
          $this->actions->removeElement($action);
          // set the owning side to null (unless already changed)
          if ($action->getUserId() === $this) {
              $action->setUserId(null);
          }
      }

      return $this;
  }


}
