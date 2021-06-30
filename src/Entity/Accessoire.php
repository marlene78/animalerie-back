<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessoireRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     * @Assert\NotBlank(
     * message = "Veuillez saisir un nom"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     * @Groups("get:infoAccessoire")
     * @Assert\NotBlank(message = "Veuillez saisir un prix")
     * @Assert\Type(type="float",message="Veuillez saisir un nombre")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="accessoires")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:infoAccessoire")
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
