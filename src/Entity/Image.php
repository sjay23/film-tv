<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_NOT_ACTIVE = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 500)]
    private ?string $link;

    #[ORM\ManyToOne(targetEntity:"App\Entity\FilmByProvider", inversedBy:"baner")]
    #[ORM\JoinColumn(name:"film_id", referencedColumnName:"id", nullable:false)]
    private $filmBaner;

    #[ORM\ManyToOne(targetEntity:"App\Entity\FilmByProvider", inversedBy:"poster")]
    #[ORM\JoinColumn(name:"film_id", referencedColumnName:"id", nullable:false)]
    private $filmPoster;


    #[ORM\Column(type:'smallint',nullable:false, options:[ "default" => 0])]
    private int $status = self::STATUS_NOT_ACTIVE;


    #[ORM\Column(type:'datetime',nullable:false, options:[ "default" => "CURRENT_TIMESTAMP"])]
    private $uploadedAt;


    #[ORM\Column(type:'datetime',nullable:false, options:[ "default" => "CURRENT_TIMESTAMP"])]
    private $uploaded;


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
    public function getFilmBaner():FilmByProvider
    {
        return $this->filmBaner;
    }

    /**
     * @param FilmByProvider $filmBaner
     */
    public function setFilmBaner(FilmByProvider $filmBaner): void
    {
        $this->filmBaner = $filmBaner;
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
     * @return DateTimeInterface
     */
    public function getUploaded(): DateTimeInterface
    {
        return $this->uploaded;
    }

    /**
     * @param DateTimeInterface $uploaded
     */
    public function setUploaded(DateTimeInterface $uploaded): void
    {
        $this->uploaded = $uploaded;
    }

}
