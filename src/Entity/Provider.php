<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=ProviderRepository::class)
 */
class Provider
{
    public const SWEET_TV = 'SweetTv';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,)
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
    public function __construct(
        string $name
    )
    {
        $this->name = $name;
        $this->tasks = new ArrayCollection();
        $this->films = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
