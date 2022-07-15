<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
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
class Country
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
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="country")
     * @JoinTable(name="film_country")

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
