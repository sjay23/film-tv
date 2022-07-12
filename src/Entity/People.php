<?php

namespace App\Entity;

use App\Repository\PeopleRepository;
use DateTimeInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=PeopleRepository::class)
 * @ORM\Table(name="`people`")
 */
class People
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255,)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     */
    private ?string $link;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable="false", options={"default": "CURRENT_TIMESTAMP"})
     */
    private DateTimeInterface $uploadedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $uploaded = false;

    /**
     * @ORM\ManyToMany(targetEntity="FilmByProvider", mappedBy="actor")
     * @JoinTable(name="film_actor")
     */
    protected ?Collection $filmActor;

    /**
     * @ORM\ManyToMany(targetEntity="FilmByProvider", mappedBy="director")
     * @JoinTable(name="film_director")
     */
    protected ?Collection $filmDirector;


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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUploaded(): bool
    {
        return $this->uploaded;
    }

    /**
     * @param bool $uploaded
     */
    public function setUploaded(bool $uploaded): void
    {
        $this->uploaded = $uploaded;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUploadedAt() :DateTimeInterface
    {
        return $this->uploadedAt;
    }

    /**
     * @param DateTimeInterface $uploadedAt
     */
    public function setUploadedAt(DateTimeInterface $uploadedAt): void
    {
        $this->uploadedAt = $uploadedAt;
    }


    /**
     * @return Collection|null
     */
    public function getFilmActor(): ?Collection
    {
        return $this->filmActor;
    }

    /**
     * @param Collection|null $filmActor
     */
    public function setFilmActor(?Collection $filmActor): void
    {
        $this->filmActor = $filmActor;
    }

    /**
     * @return Collection|null
     */
    public function getFilmDirector(): ?Collection
    {
        return $this->filmDirector;
    }

    /**
     * @param Collection|null $filmDirector
     */
    public function setFilmDirector(?Collection $filmDirector): void
    {
        $this->filmDirector = $filmDirector;
    }

}
