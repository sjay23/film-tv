<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Carbon\Carbon;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\Table(name="`image`")
 */
#[ApiResource(
    itemOperations: [
        'get',
        'delete'
    ],
    denormalizationContext: [
        'groups' => [
            'get',
            'post',
        ]
    ],
    normalizationContext: [
        'groups' => [
            'get',
            'post',
        ]
    ]
)]
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     * @Groups({"post", "get"})
     */
    private ?string $link;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProviderTranslation", mappedBy="banner")
     * @ORM\JoinTable(name="film_banner")
     * @Groups({"post", "get"})
     */
    private $filmBanner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="poster")
     * @ORM\JoinTable(name="film_poster")
     * @Groups({"post", "get"})
     */
    private $filmPoster;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable="false", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"post", "get"})
     */
    private DateTimeInterface $uploadedAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post", "get"})
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
