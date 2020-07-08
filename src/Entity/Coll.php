<?php

namespace App\Entity;

use App\Repository\CollRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CollRepository::class)
 */
class Coll
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1055, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="coll")
     */
    private $orders;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file_url;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="coll")
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=Basket::class, mappedBy="Coll")
     */
    private $baskets;

    /**
     * @ORM\ManyToMany(targetEntity=Facture::class, mappedBy="Coll")
     */
    private $factures;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="Coll")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=CategoryColl::class, mappedBy="Coll")
     */
    private $categoryColls;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->image = new ArrayCollection();
        $this->baskets = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->categoryColls = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setColl($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getColl() === $this) {
                $order->setColl(null);
            }
        }

        return $this;
    }



    public function getFileUrl(): ?string
    {
        return $this->file_url;
    }

    public function setFileUrl(?string $file_url): self
    {
        $this->file_url = $file_url;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setColl($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->image->contains($image)) {
            $this->image->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getColl() === $this) {
                $image->setColl(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Basket[]
     */
    public function getBaskets(): Collection
    {
        return $this->baskets;
    }

    public function addBasket(Basket $basket): self
    {
        if (!$this->baskets->contains($basket)) {
            $this->baskets[] = $basket;
            $basket->addColl($this);
        }

        return $this;
    }

    public function removeBasket(Basket $basket): self
    {
        if ($this->baskets->contains($basket)) {
            $this->baskets->removeElement($basket);
            $basket->removeColl($this);
        }

        return $this;
    }

    /**
     * @return Collection|Facture[]
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->addColl($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->contains($facture)) {
            $this->factures->removeElement($facture);
            $facture->removeColl($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addColl($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeColl($this);
        }

        return $this;
    }

    /**
     * @return Collection|CategoryColl[]
     */
    public function getCategoryColls(): Collection
    {
        return $this->categoryColls;
    }

    public function addCategoryColl(CategoryColl $categoryColl): self
    {
        if (!$this->categoryColls->contains($categoryColl)) {
            $this->categoryColls[] = $categoryColl;
            $categoryColl->addColl($this);
        }

        return $this;
    }

    public function removeCategoryColl(CategoryColl $categoryColl): self
    {
        if ($this->categoryColls->contains($categoryColl)) {
            $this->categoryColls->removeElement($categoryColl);
            $categoryColl->removeColl($this);
        }

        return $this;
    }
}
