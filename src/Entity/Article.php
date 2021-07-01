<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @UniqueEntity("titre")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:infoArticle")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:infoArticle")
     * @Assert\NotBlank(message = "Titre requis")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Groups("get:infoArticle")
     * @Assert\NotBlank(message = "Contenu requis")
     */
    private $contenu;

    /**
     * @Groups("get:infoArticle")
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="articles")
     */
    private $auteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
