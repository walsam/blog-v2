<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        "get" => ["normalization_context" => ["groups" => ["get-comment-with-author"]]],
        "post" => ["security" => "is_granted('ROLE_COMMENTATOR')"],
    ],
    itemOperations: [
        "get" => ["normalization_context" => ["groups" => ["get-comment-with-author"]]],
        "put" => ["security" => "is_granted('ROLE_COMMENTATOR') or object.author == user"],
    ],
    subresourceOperations: [
       "api_blog_posts_comments_get_subresource" => [
           "method" => "GET",
           "normalization_context" => ["groups"=>["get-comment-with-author"]],
        ]
    ],
    attributes: [
        "security" => "is_granted('ROLE_COMMENTATOR')",
        "order" => ["createdAt" => "DESC"],
        "pagination_items_per_page" => 10
    ],
    denormalizationContext: [
        "groups"=>["post"],
    ]
)]
class Comment Implements AuthoredEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"get-blog-post-with-author","get-comment-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-blog-post-with-author","get-comment-with-author"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-blog-post-with-author","get-comment-with-author"})
     */
    private $ModifiedAt;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $blogPost;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="childrens")
     * @Groups({"get-blog-post-with-author","get-comment-with-author"})
     */
    private $parentComent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="parentComent")
     */
    private $childrens;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author","get-comment-with-author"})
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

    public function setAuthor(?UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }
}
