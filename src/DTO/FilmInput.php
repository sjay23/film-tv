<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Provider;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FilmInput
 * @package App\Dto
 */
class FilmInput
{
    /**
     * @var Provider|null
     * @Assert\NotNull
     */
    private ?Provider $provider;

    /**
     * @var string|null
     * @Assert\NotNull
     */
    private ?string $link;

    /**
     * @var int|null
     */
    private ?int $age;

    /**
     * @var int|null
     */
    private ?int $duration;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $filmFieldTranslationInput;

    public function __construct()
    {
        $this->filmFieldTranslationInput = new ArrayCollection();
    }

    /**
     * @return Provider|null
     */
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    /**
     * @param Provider|null $provider
     */
    public function setProvider(?Provider $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * @param int|null $age
     */
    public function setAge(?int $age): void
    {
        $this->age = $age;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getFilmFieldTranslationInput(): ?ArrayCollection
    {
        return $this->filmFieldTranslationInput;
    }

    /**
     * @param FilmFieldTranslationInput $filmFieldTranslationInput
     */
    public function addFilmFieldTranslationInput(FilmFieldTranslationInput $filmFieldTranslationInput): void
    {
        $this->filmFieldTranslationInput->add($filmFieldTranslationInput);
    }
}
