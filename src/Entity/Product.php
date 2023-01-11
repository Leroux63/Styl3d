<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Le titre ne peut pas être vide')]
    #[Assert\Length(min: 3, minMessage: 'Le titre ne peut pas faire moins de {{ limit }} caractères')]
    #[Assert\Length(max: 20, maxMessage: 'Le titre ne peut pas faire plus de {{ limit }} caractères')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:'La description ne peut pas être vide')]
    #[Assert\Length(min: 20, minMessage: 'La description ne peut pas faire moins de {{ limit }} caractères')]
    #[Assert\Length(max: 500, maxMessage: 'La description ne peut pas faire plus de {{ limit }} caractères')]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: ProductCategory::class, mappedBy: 'products')]
    #[Assert\NotBlank(message:'Vous devez choisir au moins une catégorie')]
    private Collection $productCategories;

    #[ORM\Column(length: 255)]
//    #[Assert\File(
//        maxSize: '30M',
//        mimeTypes: ['zip'],
//        uploadExtensionErrorMessage: 'Merci de choisir un fichier zip valide',
//    )]
    private ?string $fileZip = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Images::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Rating::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $ratings;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Comments::class, cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Cart::class, orphanRemoval: true)]
    private Collection $carts;





    public function __construct()
    {
        $this->productCategories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->carts = new ArrayCollection();


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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public function getProductCategories(): Collection
    {
        return $this->productCategories;
    }

    public function addProductCategory(ProductCategory $productCategory): self
    {
        if (!$this->productCategories->contains($productCategory)) {
            $this->productCategories->add($productCategory);
            $productCategory->addProduct($this);
        }

        return $this;
    }

    public function removeProductCategory(ProductCategory $productCategory): self
    {
        if ($this->productCategories->removeElement($productCategory)) {
            $productCategory->removeProduct($this);
        }

        return $this;
    }

    public function getFileZip(): ?string
    {
        return $this->fileZip;
    }

    public function setFileZip(string $fileZip): self
    {
        $this->fileZip = $fileZip;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $createdAt = new \DateTime('now');
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setProduct($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getProduct() === $this) {
                $rating->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setProduct($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getProduct() === $this) {
                $cart->setProduct(null);
            }
        }

        return $this;
    }



}
