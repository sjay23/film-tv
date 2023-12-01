<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ProviderRepository::class)
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
class Provider
{
    public const SWEET_TV = 'SweetTv';
    public const MEGOGO = 'Megogo';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,)
     * @Groups({"post", "get"})
     */
    private string $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilmByProvider", mappedBy="provider",cascade={"persist", "remove"})
     */
    private Collection $films;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandTask", mappedBy="provider",cascade={"persist", "remove"})
     */
    private Collection $tasks;

    /**
     * @param string $name
     */
    #[Pure] public function __construct(
        string $name
    ) {
        $this->name = $name;
        $this->tasks = new ArrayCollection();
        $this->films = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    /**
     * @param Collection $films
     */
    public function setFilms(Collection $films): void
    {
        $this->films = $films;
    }

    /**
     * @return ArrayCollection
     */
    public function getTasks(): ArrayCollection
    {
        return $this->tasks;
    }

    /**
     * @param ArrayCollection $tasks
     */
    public function setTasks(ArrayCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
