<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FilmInput
 * @package App\Dto
 */
class QueryParameter
{
    public const ID_DESC = 0;
    public const ID_ASC = 1;
    public const YEAR_DESC = 2;
    public const YEAR_ASC = 3;
    /**
     * @var int|null
     * @Assert\NotNull
     */
    private $year;

    /**
     * @var string|null
     * @Assert\Positive
     */
    private $genreName;

    /**
     * @var string|null
     * @Assert\Positive
     */
    private $actorName;

    /**
     * @var string|null
     * @Assert\Positive
     */
    private $directorName;

    /**
     * @var float|null
     * @Assert\Positive
     */
    private $rating;

    /**
     * @var string|null
     * @Assert\Positive
     */
    private $audioLang;

    /**
     * @var string|null
     * @Assert\Positive
     */
    private $sortBy;

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return string|null
     */
    public function getGenreName(): ?string
    {
        return $this->genreName;
    }

    /**
     * @param string|null $genreName
     */
    public function setGenreName(?string $genreName): void
    {
        $this->genreName = $genreName;
    }

    /**
     * @return string|null
     */
    public function getActorName(): ?string
    {
        return $this->actorName;
    }

    /**
     * @param string|null $actorName
     */
    public function setActorName(?string $actorName): void
    {
        $this->actorName = $actorName;
    }

    /**
     * @return string|null
     */
    public function getDirectorName(): ?string
    {
        return $this->directorName;
    }

    /**
     * @param string|null $directorName
     */
    public function setDirectorName(?string $directorName): void
    {
        $this->directorName = $directorName;
    }

    /**
     * @return float|null
     */
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * @param float|null $rating
     */
    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return string|null
     */
    public function getAudioLang(): ?string
    {
        return $this->audioLang;
    }

    /**
     * @param string|null $audioLang
     */
    public function setAudioLang(?string $audioLang): void
    {
        $this->audioLang = $audioLang;
    }

    /**
     * @return string|null
     */
    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    /**
     * @param string|null $sortBy
     */
    public function setSortBy(?string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

}
