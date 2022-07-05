<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", inversedBy="genre")
     * @JoinTable(name="film_genre",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id")}
     *      )
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
