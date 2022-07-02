<?php

namespace App\Entity;

use App\Repository\AudioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AudioRepository::class)
 * @ORM\Table(name="`audio`")
 */
class Audio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var Uuid
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"post", "get", "get_answer"})
     */
    private Uuid $uuid;

    /**
     * @ORM\Column(type="string", length=30, )
     */
    private string $name;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="audio")
     */
    private $films;

    public function __construct()
    {
        $this->films= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilms(): ArrayCollection
    {
        return $this->films;
    }

    /**
     * @param ArrayCollection $films
     */
    public function setFilms(ArrayCollection $films): void
    {
        $this->films = $films;
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
