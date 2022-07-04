<?php

namespace App\Entity;

use App\Repository\PeopleRepository;
use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=PeopleRepository::class)
 * @ORM\Table(name="`people`")
 */
class People
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var Uuid
     * @ORM\Column(type="uuid", unique="true")
     * @Groups({"post", "get", "get_answer"})
     */
    private Uuid $uuid;

    /**
     * @ORM\Column(type="string", length=255,)
     */
    private ?string $name;


    /**
     * @ORM\Column(type="string", length=500, unique="true")
     */
    private ?string $link;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable="false", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $uploadedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $uploaded = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FilmByProvider", inversedBy="director")
     * @ORM\JoinColumn(name="director_id", referencedColumnName="id")
     */
    private $filmDirector;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FilmByProvider", inversedBy="actor")
     * @JoinTable(name="film_actor",
     *      joinColumns={@ORM\JoinColumn(name="actor_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_id", referencedColumnName="id")}
     *      )
     */
    private $filmActor;


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

    public function getFilmDirector()
    {
        return $this->filmDirector;
    }

    public function setFilmDirector($filmDirector): void
    {
        $this->filmDirector = $filmDirector;
    }

    public function getFilmActor(): ?People
    {
        return $this->filmActor;
    }

    public function setFilmActor(?People $filmActor): self
    {
        $this->filmActor = $filmActor;
        return $this;
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
