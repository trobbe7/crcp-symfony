<?php

namespace App\Entity;

use App\Repository\ObjectifsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectifsRepository::class)]
class Objectifs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $uid = null;

    #[ORM\Column]
    private ?float $obj_90 = null;

    #[ORM\Column]
    private ?float $obj_100 = null;

    #[ORM\Column]
    private ?float $obj_110 = null;

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

    public function getObj90(): ?float
    {
        return $this->obj_90;
    }

    public function setObj90(float $obj_90): self
    {
        $this->obj_90 = $obj_90;

        return $this;
    }

    public function getObj100(): ?float
    {
        return $this->obj_100;
    }

    public function setObj100(float $obj_100): self
    {
        $this->obj_100 = $obj_100;

        return $this;
    }

    public function getObj110(): ?float
    {
        return $this->obj_110;
    }

    public function setObj110(float $obj_110): self
    {
        $this->obj_110 = $obj_110;

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
