<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News implements \Maith\Common\AdminBundle\Interfaces\EntityMediaInterface
{
    const FULL_NEW = 1;
    const SIDE_NEW = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $Description;

    /**
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $type = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullClassName(): string
    {
        return News::class;
    }

    /**
     * @return array
     */
    public function retrieveAlbums()
    {
        return ["principal"];
    }

    /**
     * @return bool
     */
    public function checkAlbumForOnlineVideo(): bool
    {
        return true;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getDay() : string
    {
        return $this->getUpdatedAt()->format('d');
    }

    /**
     * @return string
     */
    public function getMonth() : string
    {
        $month = $this->getUpdatedAt()->format('n');
        switch ($month){
            case 1:
                return "Ene";
                break;
            case 2:
                return "Feb";
                break;
            case 3:
                return "Mar";
                break;
            case 4:
                return "Abr";
                break;
            case 5:
                return "May";
                break;
            case 6:
                return "Jun";
                break;
            case 7:
                return "Jul";
                break;
            case 8:
                return "Ago";
                break;
            case 9:
                return "Sep";
                break;
            case 10:
                return "Oct";
                break;
            case 11:
                return "Nov";
                break;
            case 12:
                return "Dic";
                break;
            default:
                return "";
                break;

        }
    }

    public function getTypeOption()
    {
        switch ($this->getType()){
            case News::FULL_NEW:
                return 'Imagen Superior';
                break;
            case News::SIDE_NEW:
                return 'Imagen lateral';
                break;
        }
        return "";
    }
}
