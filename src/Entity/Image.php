<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\Table(name="`image`")
 */
class Image
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var Uuid
     * @ORM\Column(type="uuid", unique="true")
     * @Groups({"post", "get", "get_answer"})
     */
    private Uuid $uuid;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     */
    private ?string $link;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FilmByProvider", inversedBy="banner")
     * @ORM\JoinColumn(name="film_id", referencedColumnName="id")
     */
    private $filmBanner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FilmByProvider", inversedBy="poster")
     * @ORM\JoinColumn(name="film_id", referencedColumnName="id")
     */
    private $filmPoster;


    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable="false", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $uploadedAt;


    /**
     * @ORM\Column(type="boolean")
     */
    private bool $uploaded = false;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return FilmByProvider
     */
    public function getFilmBanner():FilmByProvider
    {
        return $this->filmBanner;
    }

    /**
     * @param FilmByProvider $filmBanner
     */
    public function setFilmBanner(FilmByProvider $filmBanner): void
    {
        $this->filmBanner = $filmBanner;
    }

    /**
     * @return FilmByProvider
     */
    public function getFilmPoster():FilmByProvider
    {
        return $this->filmPoster;
    }

    /**
     * @param FilmByProvider $filmPoster
     */
    public function setFilmPoster(FilmByProvider $filmPoster): void
    {
        $this->filmPoster = $filmPoster;
    }


    /**
     * @return DateTimeInterface
     */
    public function getUploadedAt(): DateTimeInterface
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
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @param Uuid $uuid
     */
    public function setUuid(Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }

}
