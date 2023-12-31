<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PeopleRepository;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass=PeopleRepository::class)
 * @ORM\Table(name="`people`")
 */
#[ApiResource(
    collectionOperations: [
        'popular_actors' => [
            'method' => 'GET',
            'route_name' => 'api_popular',
            'openapi_context' => [
                'summary' => 'Get top labels',
                'description' => 'Get top labels'
            ],
            'add_people' => [
                'route_name' => 'add_people',
                'method' => 'POST',
                'openapi_context' => [
                    'requestBody' => [
                        'content' => [
                            'multipart/form-data' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => [
                                        'name'
                                    ],
                                    'properties' => [
                                        'name' => [
                                            'type' => 'string',
                                        ],
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
            'normalization_context' => [
                'groups' => ['get'],
            ],
        ],
    ],
    itemOperations: [
        'get',
        'update_people' => [
            'route_name' => 'update_people',
            'method' => 'PATCH',
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'application/x-www-form-urlencoded' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => [
                                        'type' => 'string',
                                    ],
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
        'delete_people' => [
            'route_name' => 'delete_people',
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
    ],

)]
class People
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255,)
     * @Groups({"post", "get"})
     */
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     * @Groups({"post", "get"})
     */
    private ?string $link;

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
