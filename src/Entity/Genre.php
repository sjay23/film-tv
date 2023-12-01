<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GenreRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'add_genre' => [
            'route_name' => 'add_genre',
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
        'update_genre' => [
            'route_name' => 'update_genre',
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
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        'delete_genre' => [
            'route_name' => 'delete_genre',
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
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get"})
     *
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="genre")
     * @JoinTable(name="film_genre")

     */
    private $films;

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

    public function getFilms(): ?FilmByProvider
    {
        return $this->films;
    }

    public function setFilms(?FilmByProvider $films): self
    {
        $this->films = $films;
        return $this;
    }

}
