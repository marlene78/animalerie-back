<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnimauxRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AnimauxRepository::class)
 */
class Animaux
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:infoFood")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:infoFood")
     */
    private $race;

    /**
     * @ORM\Column(type="float")
     * @Groups("get:infoFood")
     */
    private $poids;

    /**
     * @ORM\Column(type="integer")
     * @Groups("get:infoFood")
     */
    private $age;

    /**
     * @ORM\Column(type="float")
     * @Groups("get:infoFood")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="animauxes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:infoFood")
     */
    private $type;





    public function getId(): ?int
    {
        return $this->id;
    }



    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): self
    {
        $this->poids = $poids;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }
}
