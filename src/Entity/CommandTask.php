<?php

namespace App\Entity;

use App\Repository\CommandTaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandTaskRepository::class)
 */
class CommandTask
{
    public const STATUS_ERROR = 2;
    public const STATUS_WORK = 1;
    public const STATUS_NOT_WORK = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "get"})
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $countTask = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Provider", inversedBy="tasks")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     * @Groups({"post", "get"})
     */
    private ?Provider $provider;

    /**
     * @ORM\Column(type="integer",nullable="true")
     * @Groups({"post", "get"})
     */
    private int $lastId;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     * @Groups({"post", "get"})
     */
    private int $status = self::STATUS_NOT_WORK;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private ?string $descriptionStatus = null;

    /**
     * @param string $name
     */
    public function __construct(
        string $name
    ) {
        $this->name = $name;
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

    public function getLastId(): ?int
    {
        return $this->lastId;
    }

    public function setLastId(int $lastId): self
    {
        $this->lastId = $lastId;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCountTask(): ?int
    {
        return $this->countTask;
    }

    public function setCountTask(int $countTask): self
    {
        $this->countTask = $countTask;

        return $this;
    }

    public function getDescriptionStatus(): ?string
    {
        return $this->descriptionStatus;
    }

    public function setDescriptionStatus(?string $descriptionStatus): self
    {
        $this->descriptionStatus = $descriptionStatus;

        return $this;
    }
}
