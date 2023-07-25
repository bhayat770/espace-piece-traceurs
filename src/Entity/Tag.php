<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    private $slugify;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'tags')]
    private Collection $product;

    #[ORM\ManyToMany(targetEntity: Traceurs::class, mappedBy: 'tags')]
    private Collection $traceurs;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->traceurs = new ArrayCollection();
    }


    public function getSlug(): ?string
    {
        return $this->slugify->slug($this->nom);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->product->removeElement($product);

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
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
            $traceur->addTag($this);
        }

        return $this;
    }

    public function removeTraceur(Traceurs $traceur): self
    {
        if ($this->traceurs->removeElement($traceur)) {
            $traceur->removeTag($this);
        }

        return $this;
    }

}
