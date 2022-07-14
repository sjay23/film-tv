<?php

namespace App\Entity;

use App\Repository\FilmByProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FilmByProviderRepository::class)
 * @ORM\Table(name="film_by_provider")
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
class FilmByProvider implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", length=30)
     * @ORM\JoinColumn(name="movie_id", nullable="false")
     * @Groups({"post", "get"})
     */
    private $movieId;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     * @Groups({"post", "get"})
     */
    private ?string $link;

    /**
     * @ORM\Column(type="smallint" ,length=4, nullable="true")
     * @Groups({"post", "get"})
     */
    private $year;

    /**
     * @ORM\Column(type="decimal", precision="4", scale="2", nullable="true")
     * @Groups({"post", "get"})
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=5,nullable="true")
     * @Groups({"post", "get"})
     */
    private ?string $age;

    /**
     * @ORM\Column(type="integer" ,nullable="true")
     * @Groups({"post", "get"})
     */
    private int $duration;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", inversedBy="filmPoster")
     * @ORM\JoinTable(name="film_poster",
     *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $poster;

    /**
     * @ORM\ManyToMany(targetEntity="People", inversedBy="films")
     * @ORM\JoinTable(name="film_actor",
     *      joinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="actor_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $actor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", inversedBy="films")
     * @JoinTable(name="film_country",
     *      joinColumns={@ORM\JoinColumn(name="country_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $country;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", inversedBy="films")
     * @JoinTable(name="film_genre",
     *      joinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $genre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Audio", inversedBy="films")
     * @JoinTable(name="film_audio",
     *      joinColumns={@ORM\JoinColumn(name="audio_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @Groups({"post", "get"})
     */
    private Collection $audio;

    /**
     * @ORM\ManyToMany(targetEntity="People", inversedBy="filmDirector")
     * @ORM\JoinTable(name="film_director",
     *      joinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="director_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $director;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="films")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     * @Groups({"post", "get"})
     */
    private ?Provider $provider;

    public function __construct()
    {
        $this->audio = new ArrayCollection();
        $this->actor = new ArrayCollection();
        $this->director = new ArrayCollection();
        $this->country = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->poster = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
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

    /**
     * @return Collection|Image[]
     */
    public function getPoster(): Collection
    {
        return $this->poster;
    }


    public function setPoster(Image $poster): self
    {
        if (!$this->poster->contains($poster)) {
            $this->poster[] = $poster;
        }
        return $this;
    }

    /**
     * @return Collection|People[]
     */
    public function getActor(): Collection
    {
        return $this->actor;
    }


    public function setActor(People $actor): self
    {
        if (!$this->actor->contains($actor)) {
            $this->actor[] = $actor;
        }
        return $this;
    }

    /**
     * @return Collection|Audio []
     */
    public function getAudio(): Collection
    {
        return $this->audio;
    }


    public function setAudio(Audio $audio): self
    {

        if (!$this->audio->contains($audio)) {
            $this->audio[] = $audio;
        }
        return $this;
    }

    /**
     * @return Collection|People[]
     */
    public function getDirector(): Collection
    {
        return $this->director;
    }


    public function setDirector(People $director): self
    {
        if (!$this->director->contains($director)) {
            $this->director[] = $director;
        }
        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     */
    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }


    public function setGenre(Genre $genre): self
    {
        if (!$this->genre->contains($genre)) {
            $this->genre[] = $genre;
        }
        return $this;
    }

    /**
     * @return Collection|Country []
     */
    public function getCountry (): Collection
    {
        return $this->country;
    }


    public function setCountry (Country $country): self
    {
        if (!$this->country->contains($country)) {
            $this->country[] = $country;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovieId()
    {
        return $this->movieId;
    }

    /**
     * @param mixed $movieId
     */
    public function setMovieId($movieId): void
    {
        $this->movieId = $movieId;
    }
}
