<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ImageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Carbon\Carbon;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ORM\Table(name="`image`")
 * @Vich\Uploadable
 */
#[ApiResource(
    collectionOperations: [
        'add_image' => [
            'route_name' => 'add_image',
            'method' => 'POST',
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'application/x-www-form-urlencoded' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'images' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'get'
    ],
    itemOperations: [
        'get',
        'update_image' => [
            'route_name' => 'update_image',
            'method' => 'PATCH',
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'application/x-www-form-urlencoded' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'link' => [
                                        'type' => 'string',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'delete_image' => [
            'route_name' => 'delete_image',
            'method' => 'DELETE'
        ],
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
    public const UPLOAD = 1;
    public const NO_UPLOAD = 0;
    private ImageRepository $imageRepository;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=500, unique="true",nullable="true")
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
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    public ?string $filePath = null;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     * @Groups({"post", "get"})
     */
    private int $uploaded = self::NO_UPLOAD;

    /**
     * @Assert\NotNull(groups={"user"})
     * @Assert\NotBlank
     * @Vich\UploadableField(mapping="image", fileNameProperty="filePath")
     */
    public $imageFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $imageName;


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
     * @return int
     */
    public function getUploaded(): int
    {
        return $this->uploaded;
    }

    /**
     * @param int $uploaded
     */
    public function setUploaded(int $uploaded): void
    {
        $this->uploaded = $uploaded;
    }

    public function setImageFile($imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     */
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->imageName,
        ]);
    }

    public function unserialize($data)
    {
        [$this->id] = unserialize($data);
    }
}
