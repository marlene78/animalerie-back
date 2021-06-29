<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 * @UniqueEntity("nom")
 */
class Type
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:infoType")
     * @Groups("get:infoFood")
     * @Groups("get:infoAnimaux")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:infoFood")
     * @Groups("get:infoAnimaux")
     * @Groups("get:infoType")
     * @Assert\NotBlank(
     * message = "Veuillez saisir un nom"
     * )
     */
    private $nom;



    /**
     * @ORM\OneToMany(targetEntity=Accessoire::class, mappedBy="type" , cascade={"persist", "remove"})
     */
    private $accessoires;

    /**
     * @ORM\OneToMany(targetEntity=Animaux::class, mappedBy="type")
     */
    private $animauxes;

    /**
     * @ORM\OneToMany(targetEntity=Nourriture::class, mappedBy="type")
     */
    private $nourritures;



    
    public function __construct()
    {
        $this->accessoires = new ArrayCollection();
        $this->animauxes = new ArrayCollection();
        $this->nourritures = new ArrayCollection();
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
     * @return Collection|Accessoire[]
     */
    public function getAccessoires(): Collection
    {
        return $this->accessoires;
    }

    public function addAccessoire(Accessoire $accessoire): self
    {
        if (!$this->accessoires->contains($accessoire)) {
            $this->accessoires[] = $accessoire;
            $accessoire->setType($this);
        }

        return $this;
    }

    public function removeAccessoire(Accessoire $accessoire): self
    {
        if ($this->accessoires->removeElement($accessoire)) {
            // set the owning side to null (unless already changed)
            if ($accessoire->getType() === $this) {
                $accessoire->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Animaux[]
     */
    public function getAnimauxes(): Collection
    {
        return $this->animauxes;
    }

    public function addAnimaux(Animaux $animaux): self
    {
        if (!$this->animauxes->contains($animaux)) {
            $this->animauxes[] = $animaux;
            $animaux->setType($this);
        }

        return $this;
    }

    public function removeAnimaux(Animaux $animaux): self
    {
        if ($this->animauxes->removeElement($animaux)) {
            // set the owning side to null (unless already changed)
            if ($animaux->getType() === $this) {
                $animaux->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Nourriture[]
     */
    public function getNourritures(): Collection
    {
        return $this->nourritures;
    }

    public function addNourriture(Nourriture $nourriture): self
    {
        if (!$this->nourritures->contains($nourriture)) {
            $this->nourritures[] = $nourriture;
            $nourriture->setType($this);
        }

        return $this;
    }

    public function removeNourriture(Nourriture $nourriture): self
    {
        if ($this->nourritures->removeElement($nourriture)) {
            // set the owning side to null (unless already changed)
            if ($nourriture->getType() === $this) {
                $nourriture->setType(null);
            }
        }

        return $this;
    }

    
}
