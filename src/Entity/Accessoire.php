<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessoireRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AccessoireRepository::class)
 */
class Accessoire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:infoAccessoire")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:infoAccessoire")
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     * @Groups("get:infoAccessoire")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="accessoires")
<<<<<<< HEAD
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:infoAccessoire")
=======
>>>>>>> acc396f077ad5ad85b10245bb5b160c6d07529e7
     */
    private $type;


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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function __toString()
    {
    return $this->nom;
    }
}
