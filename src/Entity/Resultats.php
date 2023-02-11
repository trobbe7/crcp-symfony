<?php

namespace App\Entity;

use App\Repository\ResultatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatsRepository::class)]
class Resultats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $uid = null;

    #[ORM\Column]
    private ?int $tel = null;

    #[ORM\Column]
    private ?int $mail = null;

    #[ORM\Column]
    private ?int $correspondances = null;

    #[ORM\Column]
    private ?int $traitements = null;

    #[ORM\Column]
    private ?float $full_time = null;

    #[ORM\Column]
    private ?float $time_minutes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?int
    {
        return $this->mail;
    }

    public function setMail(int $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getCorrespondances(): ?int
    {
        return $this->correspondances;
    }

    public function setCorrespondances(int $correspondances): self
    {
        $this->correspondances = $correspondances;

        return $this;
    }

    public function getTraitements(): ?int
    {
        return $this->traitements;
    }

    public function setTraitements(int $traitements): self
    {
        $this->traitements = $traitements;

        return $this;
    }

    public function getFullTime(): ?float
    {
        return $this->full_time;
    }

    public function setFullTime(float $full_time): self
    {
        $this->full_time = $full_time;

        return $this;
    }

    public function getTimeMinutes(): ?float
    {
        return $this->time_minutes;
    }

    public function setTimeMinutes(float $time_minutes): self
    {
        $this->time_minutes = $time_minutes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
