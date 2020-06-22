<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BasketRepository::class)
 */
class Basket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="basket")
     */
    private $order1;

    /**
     * @ORM\ManyToOne(targetEntity=UserInfo::class, inversedBy="baskets")
     */
    private $userinfo;

    /**
     * @ORM\OneToOne(targetEntity=Ship::class, cascade={"persist", "remove"})
     */
    private $ship;

    public function __construct()
    {
        $this->order1 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrder1(): Collection
    {
        return $this->order1;
    }

    public function addOrder1(Order $order1): self
    {
        if (!$this->order1->contains($order1)) {
            $this->order1[] = $order1;
            $order1->setBasket($this);
        }

        return $this;
    }

    public function removeOrder1(Order $order1): self
    {
        if ($this->order1->contains($order1)) {
            $this->order1->removeElement($order1);
            // set the owning side to null (unless already changed)
            if ($order1->getBasket() === $this) {
                $order1->setBasket(null);
            }
        }

        return $this;
    }

    public function getUserinfo(): ?UserInfo
    {
        return $this->userinfo;
    }

    public function setUserinfo(?UserInfo $userinfo): self
    {
        $this->userinfo = $userinfo;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): self
    {
        $this->ship = $ship;

        return $this;
    }
}
