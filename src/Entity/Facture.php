<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="factures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToMany(targetEntity=Coll::class, inversedBy="factures")
     */
    private $Coll;

    public function __construct()
    {
        $this->Coll = new ArrayCollection();
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
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection|Coll[]
     */
    public function getColl(): Collection
    {
        return $this->Coll;
    }

    public function addColl(Coll $coll): self
    {
        if (!$this->Coll->contains($coll)) {
            $this->Coll[] = $coll;
        }

        return $this;
    }

    public function removeColl(Coll $coll): self
    {
        if ($this->Coll->contains($coll)) {
            $this->Coll->removeElement($coll);
        }

        return $this;
    }

}
