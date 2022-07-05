<?php

namespace App\Entity;

use App\Repository\FilmByProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * @ORM\Entity(repositoryClass=FilmByProviderRepository::class)
 * @ORM\Table(name="film_by_provider")
 */
class FilmByProvider implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", length=30)
     * @ORM\JoinColumn(name="movie_id", nullable="false")
     */
    private $movieId;

    /**
     * @ORM\Column(type="string", length=500, unique="true")
     */
    private ?string $link;

    /**
     * @ORM\Column(type="smallint" ,length=4, nullable="true")
     */
    private $year;

    /**
     * @ORM\Column(type="decimal", precision="4", scale="2", nullable="true")
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=5,nullable="true")
     */
    private ?string $age;

    /**
     * @ORM\Column(type="integer" ,nullable="true")
     */
    private int $duration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="filmBanner",cascade={"persist", "remove"})
     * @ORM\JoinColumn( nullable="true")
     */
    private $banner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="filmPoster",cascade={"persist", "remove"})
     * @ORM\JoinColumn( nullable="true")
     */
    private $poster;

    /**
     * @ORM\ManyToMany(targetEntity="People", inversedBy="films")
     * @ORM\JoinTable(name="film_actor",
     *      joinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="actor_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private ?Collection $actor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", mappedBy="films")
     * @JoinTable(name="film_country")
     */
    private ?Collection $country;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", mappedBy="films")
     * @JoinTable(name="film_genre")
     */
    private ?Collection $genre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Audio", mappedBy="films")
     * @JoinTable(name="film_audio")
     */
    private Collection $audio;

    /**
     * @ORM\ManyToMany(targetEntity="People", inversedBy="filmDirector")
     * @ORM\JoinTable(name="film_director",
     *      joinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="director_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private ?Collection $director;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="films")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private ?Provider $provider;

    public function __construct()
    {
        $this->audio = new ArrayCollection();
        $this->actor = new ArrayCollection();
        $this->director = new ArrayCollection();
        $this->country = new ArrayCollection();
        $this->genre = new ArrayCollection();
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
        $this->banner = $banner;
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
