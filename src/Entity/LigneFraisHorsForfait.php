<?php

namespace App\Entity;

use App\Repository\LigneFraisHorsForfaitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFraisHorsForfaitRepository::class)]
class LigneFraisHorsForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisHorsForfaits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FicheFrais $fichefrais = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    /**
     * @param FicheFrais|null $fichefrais
     * @param string|null $libelle
     * @param \DateTimeInterface|null $date
     * @param string|null $montant
     */
    public function __construct(?FicheFrais $fichefrais, ?string $libelle, ?\DateTimeInterface $date, ?string $montant)
    {
        $this->fichefrais = $fichefrais;
        $this->libelle = $libelle;
        $this->date = $date;
        $this->montant = $montant;
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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
}
