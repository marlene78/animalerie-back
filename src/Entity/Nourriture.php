<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NourritureRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=NourritureRepository::class)
 * @UniqueEntity("nom")
 */
class Nourriture
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
     * @Assert\NotBlank(message = "Veuillez saisir un nom")
     * @Groups("get:infoFood")
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Veuillez saisir une description")
     * @Groups("get:infoFood")
     */
    private $description;



    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message = "Veuillez saisir un prix")
     * @Assert\Type(type="float",message="Veuillez saisir un nombre")
     * @Groups("get:infoFood")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="nourritures" , cascade={"persist", "remove"})
     * @Groups("get:infoFood")
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
