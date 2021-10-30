<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiSubresource;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        "get" => ["normalization_context" => ["groups" => ["get-blog-post-with-author"]]],
        "post" => ["security" => "is_granted('ROLE_WRITER')"],
    ],
    itemOperations: [
        "get" => ["normalization_context" => ["groups" => ["get-blog-post-with-author"]]],
        "put" => ["security" => "is_granted('ROLE_WRITER') or object.author == user"],
    ],
    attributes: [
        "security" => "is_granted('ROLE_COMMENTATOR')",
        "order" => ["createdAt" => "DESC"],
        "pagination_items_per_page" => 10
    ],
    denormalizationContext: [
        "groups" => ["postBlogPost"]
    ]
)]
class BlogPost Implements AuthoredEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-blog-post-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"postBlogPost","get-blog-post-with-author"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"postBlogPost","get-blog-post-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"postBlogPost","get-blog-post-with-author"})
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-blog-post-with-author"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"get-blog-post-with-author"})
     */
    private $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="blogPost")
     * @Groups({"get-blog-post-with-author"})
     * @ApiSubresource()
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-blog-post-with-author"})
     */
    private $author;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->setCreatedAt();
        $this->setModifiedAt();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(): self
    {
        $this->modifiedAt = new \DateTimeImmutable();

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
            $comment->setBlogPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlogPost() === $this) {
                $comment->setBlogPost(null);
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
