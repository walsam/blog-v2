<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
#[ApiResource]
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $ModifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blogPost;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="childrens")
     */
    private $parentComent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parentComent")
     */
    private $childrens;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function __construct()
    {
        $this->childrens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->ModifiedAt;
    }

    public function setModifiedAt(\DateTimeImmutable $ModifiedAt): self
    {
        $this->ModifiedAt = $ModifiedAt;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function getParentComent(): ?self
    {
        return $this->parentComent;
    }

    public function setParentComent(?self $parentComent): self
    {
        $this->parentComent = $parentComent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(self $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens[] = $children;
            $children->setParentComent($this);
        }

        return $this;
    }

    public function removeChildren(self $children): self
    {
        if ($this->childrens->removeElement($children)) {
            // set the owning side to null (unless already changed)
            if ($children->getParentComent() === $this) {
                $children->setParentComent(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
