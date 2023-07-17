<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\String_;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Vich\Uploadable]

class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255)]
  /*  /**
     * @ORM\ManyToOne(targetEntity="Illustration", mappedBy="product", cascade={"persist"})
     */
    private ?string $illustration = null;

    #[Vich\UploadableField(mapping: 'product', fileNameProperty: 'illustration')]
    private ?File $illustrationFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $partnumber = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Marque $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $poids = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, cascade: ['persist'], orphanRemoval: true,)]
    private Collection $productImages;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'product')]
    #[ORM\JoinTable(name: 'product_tag')]
    private Collection $tags;

    #[ORM\Column]
    private ?bool $isBest = null;

    #[ORM\Column]
    private ?bool $enPromo = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixPromo = null;

    #[ORM\OneToMany(mappedBy: 'Produit', targetEntity: OrderDetails::class)]
    private Collection $orderDetails;

    #[ORM\OneToMany(mappedBy: 'Produit', targetEntity: OrderDetails::class)]
    private Collection $orderDetail;

    #[ORM\Column]
    private ?bool $bestCartouches = null;

    #[ORM\Column]
    private ?bool $bestSellers = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Traceurs::class, mappedBy: 'Product')]
    private Collection $traceurs;


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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

   /* public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    } */

    public function setIllustrationFile(?File $illustrationFile = null) : void
    {
        $this->illustrationFile = $illustrationFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if (null !== $illustrationFile)
        {
            $this->updatedAt = new \DateTimeImmutable('now');
        }
    }

    public function getIllustrationFile() :?File
    {
        return $this->illustrationFile;
    }

    public function setIllustration(?string $illustration) : self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getIllustration() : ?string
    {
        return $this->illustration;
    }

    public function __construct()
    {
        $this->illustration = new ArrayCollection();
        $this->productImages = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
        $this->orderDetail = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->traceurs = new ArrayCollection();
    }




    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPartnumber(): ?string
    {
        return $this->partnumber;
    }

    public function setPartnumber(string $partnumber): self
    {
        $this->partnumber = $partnumber;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(string $poids): self
    {
        $this->poids = $poids;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * @return Collection<int, ProductImage>
     */
    public function getProductImages(): Collection
    {
        return $this->productImages;
    }

    public function addProductImage(ProductImage $productImage): self
    {
        if (!$this->productImages->contains($productImage)) {
            $this->productImages->add($productImage);
            $productImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductImage(ProductImage $productImage): self
    {
        if ($this->productImages->removeElement($productImage)) {
            // set the owning side to null (unless already changed)
            if ($productImage->getProduct() === $this) {
                $productImage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addProduct($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeProduct($this);
        }

        return $this;
    }

    public function getAvailableQuantity(): int
    {
        return $this->quantite;
    }

    public function isIsBest(): ?bool
    {
        return $this->isBest;
    }

    public function setIsBest(bool $isBest): self
    {
        $this->isBest = $isBest;

        return $this;
    }

    public function isEnPromo(): ?bool
    {
        return $this->enPromo;
    }

    public function setEnPromo(bool $enPromo): self
    {
        $this->enPromo = $enPromo;

        return $this;
    }

    public function getPrixPromo(): ?float
    {
        return $this->prixPromo;
    }

    public function setPrixPromo(?float $prixPromo): self
    {
        $this->prixPromo = $prixPromo;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProduit($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduit() === $this) {
                $orderDetail->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetail(): Collection
    {
        return $this->orderDetail;
    }

    public function isBestCartouches(): ?bool
    {
        return $this->bestCartouches;
    }

    public function setBestCartouches(bool $bestCartouches): self
    {
        $this->bestCartouches = $bestCartouches;

        return $this;
    }
    /**
     * Check if the product has a specific tag.
     *
     * @param Tag $tag
     * @return bool
     */
    public function hasTag(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function isBestSellers(): ?bool
    {
        return $this->bestSellers;
    }

    public function setBestSellers(bool $bestSellers): self
    {
        $this->bestSellers = $bestSellers;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }
public function __toString(): string
{
    return $this->name;
}

/**
 * @return Collection<int, Traceurs>
 */
public function getTraceurs(): Collection
{
    return $this->traceurs;
}

public function addTraceur(Traceurs $traceur): self
{
    if (!$this->traceurs->contains($traceur)) {
        $this->traceurs->add($traceur);
        $traceur->addProduct($this);
    }

    return $this;
}

public function removeTraceur(Traceurs $traceur): self
{
    if ($this->traceurs->removeElement($traceur)) {
        $traceur->removeProduct($this);
    }

    return $this;
}

}
