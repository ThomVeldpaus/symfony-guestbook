<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The Conference Entity class is a normal PHP class
 * without Doctrine influence. Annotations are used
 * to add metadata that will will connect to the right
 * database table.
 *
 * The Entity Conference has a OneToMany relationship
 * with the Comment Entity
 *
 * @ORM\Entity(repositoryClass=ConferenceRepository::class)
 */
class Conference
{
    /**
     * Doctrine added the id property to keep the primary key in the row
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isInternational;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="conference", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * By adding this __toString function, the display on the
     * admin frontend will be better then only by primary key.     *
     * It will not be like Conference #1, Conference #2 anymore,
     * but this string that this function returns.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->city.' '.$this->year;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getIsInternational(): ?bool
    {
        return $this->isInternational;
    }

    public function setIsInternational(bool $isInternational): self
    {
        $this->isInternational = $isInternational;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setConference($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getConference() === $this) {
                $comment->setConference(null);
            }
        }

        return $this;
    }
}
