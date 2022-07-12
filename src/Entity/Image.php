<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Carbon\Carbon;

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
     * @ORM\Column(type="string", length=500, unique="true")
     */
    private ?string $link;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProviderTranslation", mappedBy="banner")
     * @ORM\JoinTable(name="film_banner")
     */
    private $filmBanner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="poster")
     * @ORM\JoinTable(name="film_poster")
     */
    private $filmPoster;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable="false", options={"default": "CURRENT_TIMESTAMP"})
     */
    private DateTimeInterface $uploadedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $uploaded;

    public function __construct(
        ?string $link
    )
    {
        $this->link = $link;
        $this->uploaded = false;
        $this->uploadedAt = Carbon::now();
    }

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
}
