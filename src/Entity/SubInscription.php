<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubInscriptionRepository")
 */
class SubInscription
{

    const LEVEL_ONE = 1;
    const LEVEL_TWO = 2;
    const LEVEL_THREE = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="date")
     */
    private $startdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SubInscriptionSections")
     */
    private $sections;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $approved;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIdentity(): ?string
    {
        return $this->identity;
    }

    public function setIdentity(string $identity): self
    {
        $this->identity = $identity;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return Collection|SubInscriptionSections[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(SubInscriptionSections $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
        }

        return $this;
    }

    public function removeSection(SubInscriptionSections $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
        }

        return $this;
    }

    public function getLevelAsString() : string
    {
        $choices = self::getLevelsCombo();
        foreach ($choices as $key => $value) {
            if ($value == $this->getLevel()) {
                return $key;
            }
        }
        return "";
    }

    public static function getLevelsCombo(): array
    {
        return [
            'Nivel 1' => SubInscription::LEVEL_ONE,
            'Nivel 2' => SubInscription::LEVEL_TWO,
            'Nivel 3' => SubInscription::LEVEL_THREE,
        ];
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(?bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }
}
