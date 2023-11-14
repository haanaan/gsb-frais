<?php

namespace App\Entity;

use App\Repository\LigneFraisForfaitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFraisForfaitRepository::class)]
class LigneFraisForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisForfaits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FicheFrais $fichefrais = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisForfaits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FraisForfait $fraisforfait = null;

    #[ORM\Column]
    private ?int $quantite = null;

    /**
     * @param FicheFrais|null $fichefrais
     * @param FraisForfait|null $fraisforfait
     * @param int|null $quantite
     */
    public function __construct(?FicheFrais $fichefrais, ?FraisForfait $fraisforfait, ?int $quantite)
    {
        $this->fichefrais = $fichefrais;
        $this->fraisforfait = $fraisforfait;
        $this->quantite = $quantite;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichefrais(): ?FicheFrais
    {
        return $this->fichefrais;
    }

    public function setFichefrais(?FicheFrais $fichefrais): self
    {
        $this->fichefrais = $fichefrais;

        return $this;
    }

    public function getFraisforfait(): ?FraisForfait
    {
        return $this->fraisforfait;
    }

    public function setFraisforfait(?FraisForfait $fraisforfait): self
    {
        $this->fraisforfait = $fraisforfait;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}
