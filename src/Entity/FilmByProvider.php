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

    #[ORM\Column(type: 'date')]
    private $year;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2)]
    private $rating;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $country;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $age;

    #[ORM\Column(type: 'integer')]
    private int $duration;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $idByProvider;


    #[ORM\OneToMany(targetEntity: "App\Entity\Image", mappedBy: "filmBaner", cascade: ["persist", "remove"])]
    private $baner;


    #[ORM\OneToMany(targetEntity: "App\Entity\Image", mappedBy: "filmPoster", cascade: ["persist", "remove"])]
    private $poster;


    #[ORM\ManyToMany(targetEntity: "App\Entity\People", inversedBy: "filmActor")]
    #[ORM\JoinColumn(name: "filmActor_id", referencedColumnName: "id", nullable: false)]
    private $actor;


    #[ORM\ManyToMany(targetEntity: "App\Entity\People", inversedBy: "filmDirector")]
    #[ORM\JoinColumn(name: "filmDirector_id", referencedColumnName: "id", nullable: false)]
    private $derector;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Audio", inversedBy: "films")]
    #[ORM\JoinColumn(name: "audio", referencedColumnName: "audioLanguage", nullable: false)]
    private $audio;

    public function __construct()
    {
        $this->derector = new ArrayCollection();
        $this->actor = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getBaner()
    {
        return $this->baner;
    }

    /**
     * @param mixed $baner
     */
    public function setBaner($baner): void
    {
        $this->baner = $baner;
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

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
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

    public function getIdByProvider(): ?int
    {
        return $this->idByProvider;
    }

    public function setIdByProvider(int $idByProvider): self
    {
        $this->idByProvider = $idByProvider;

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
    public function getDerector(): Collection
    {
        return $this->derector;
    }

    /**
     * @param Collection $derector
     */
    public function setDerector(Collection $derector): void
    {
        $this->derector = $derector;
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
