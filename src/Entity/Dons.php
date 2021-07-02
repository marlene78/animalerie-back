<?php

namespace App\Entity;


use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DonsRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;




/**
 * @ORM\Entity(repositoryClass=DonsRepository::class)
 */
class Dons
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:infoDons")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message = "Veuillez saisir un montant")
     * @Assert\Type(type="float",message="Veuillez saisir un nombre")
     * @Groups("get:infoDons")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("get:infoDons")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="dons")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:infoDons")
     */
    private $user;





    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}
