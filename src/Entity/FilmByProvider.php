<?php

namespace App\Entity;

use App\Repository\FilmByProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmByProviderRepository::class)]
class FilmByProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 500, unique: true)]
    private ?string $link;

    #[ORM\Column(type: 'string', length: 5000)]
    private ?string $description;

    #[ORM\Column(type: 'integer')]
    private $year;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2)]
    private $rating;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $country;

    #[ORM\Column(type: 'string', length: 5)]
    private ?string $age;

    #[ORM\Column(type: 'integer')]
    private int $duration;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $providerId ;


    #[ORM\OneToMany(targetEntity: "App\Entity\Image", mappedBy: "filmBanner", cascade: ["persist", "remove"])]
    private $banner;



    #[ORM\OneToMany(targetEntity: "App\Entity\Image", mappedBy: "filmPoster", cascade: ["persist", "remove"])]
    #[ORM\JoinColumn( nullable: false)]
    private $poster;


    #[ORM\ManyToMany(targetEntity: "App\Entity\People", inversedBy: "filmActor")]
    #[ORM\JoinColumn(name: "filmActor_id", referencedColumnName: "id", nullable: true)]
    private $actor;


    #[ORM\ManyToMany(targetEntity: "App\Entity\People", inversedBy: "filmDirector")]
    #[ORM\JoinColumn(name: "filmDirector_id", referencedColumnName: "id", nullable: true)]
    private $director;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Audio", inversedBy: "films")]
    #[ORM\JoinColumn(name: "audio", referencedColumnName: "audioLanguage", nullable: true)]
    private $audio;

    public function __construct()
    {
        $this->director = new ArrayCollection();
        $this->actor = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @param mixed $banner
     */
    public function setBanner($banner): void
    {
        $this->baner = $banner;
    }

    /**
     * @return mixed
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param mixed $poster
     */
    public function setPoster($poster): void
    {
        $this->poster = $poster;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year): void
    {
        $this->year = $year;
    }



    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating): void
    {
        $this->rating = $rating;
    }


    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getProviderId(): ?int
    {
        return $this->providerId;
    }

    public function setProviderId(int $providerId): self
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * @return Collection|People[]
     */
    public function getActor(): Collection
    {
        return $this->actor;
    }

    /**
     * @param Collection $actor
     */
    public function setActor(Collection $actor): void
    {
        $this->actor = $actor;
    }

    /**
     * @return Collection|People[]
     */
    public function getDirector(): Collection
    {
        return $this->director;
    }

    /**
     * @param Collection $director
     */
    public function setDirector(Collection $director): void
    {
        $this->director = $director;
    }

    /**
     * @return Audio
     */
    public function getAudio(): ?Audio
    {
        return $this->audio;
    }

    /**
     * @param Audio $audio
     */
    public function setAudio(Audio $audio): void
    {
        $this->audio = $audio;
    }

}
