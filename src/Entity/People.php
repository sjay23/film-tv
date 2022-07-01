<?php

namespace App\Entity;

use App\Repository\PeopleRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
class People
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 500, unique: true)]
    private ?string $link;


    #[ORM\Column(type:'datetime',nullable:false, options:[ "default" => "CURRENT_TIMESTAMP"])]
    private $uploadedAt;


    #[ORM\Column(type:'datetime',nullable:false, options:[ "default" => "CURRENT_TIMESTAMP"])]
    private $uploaded;


    #[ORM\ManyToMany(targetEntity:"App\Entity\FilmByProvider", mappedBy:"filmDirector")]
    private $filmDirector;

    #[ORM\ManyToMany(targetEntity:"App\Entity\FilmByProvider", mappedBy:"filmActor")]
    private $filmActor;

    public function __construct()
    {
        $this->filmActor = new ArrayCollection();
        $this->filmDirector = new ArrayCollection();

    }

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
     *  @return  Collection|FilmByProvider[]
     */
    public function getFilmDirector(): Collection
    {
        return $this->filmDirector;
    }

    /**
     * @param Collection $filmDirector
     */
    public function setFilmDirector(Collection $filmDirector): void
    {
        $this->filmDirector = $filmDirector;
    }

    /**
     * @return  Collection|FilmByProvider[]
     */
    public function getFilmActor(): Collection
    {
        return $this->filmActor;
    }

    /**
     * @param Collection $filmActor
     */
    public function setFilmActor(Collection $filmActor): void
    {
        $this->filmActor = $filmActor;
    }



}
