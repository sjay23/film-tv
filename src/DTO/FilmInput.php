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
     * @var int|null
     */
    private ?int $movieId;

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
    private ?int $years;

    /**
     * @var int|null
     */
    private ?int $rating;

    /**
     * @var int|null
     */
    private ?int $duration;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $genreInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $audioInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $castInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $countryInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $imageInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $directorInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $filmFieldTranslationInput;

    public function __construct()
    {
        $this->filmFieldTranslationInput = new ArrayCollection();
        $this->countryInput = new ArrayCollection();
        $this->directorInput = new ArrayCollection();
        $this->castInput = new ArrayCollection();
        $this->audioInput = new ArrayCollection();
        $this->imageInput = new ArrayCollection();
        $this->genreInput = new ArrayCollection();

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
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }


    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return int|null
     */
    public function getMovieId(): ?int
    {
        return $this->movieId;
    }

    /**
     * @param int|null $movieId
     */
    public function setMovieId(?int $movieId): void
    {
        $this->movieId = $movieId;
    }

    /**
     * @return int|null
     */
    public function getYears(): ?int
    {
        return $this->years;
    }


    /**
     * @param int|null $years
     */
    public function setYears(?int $years): void
    {
        $this->years = $years;
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

    /**
     * @return ArrayCollection|null
     */
    public function getAudioInput(): ?ArrayCollection
    {
        return $this->audioInput;
    }

    /**
     * @param AudioInput $audioInput
     */
    public function addAudioInput(AudioInput $audioInput): void
    {
        $this->audioInput->add($audioInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getCastInput(): ?ArrayCollection
    {
        return $this->castInput;
    }

    /**
     * @param CastInput $castInput
     */
    public function addCastInput(CastInput $castInput): void
    {
        $this->castInput->add($castInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getCountryInput(): ?ArrayCollection
    {
        return $this->countryInput;
    }

    /**
     * @param ArrayCollection|null $countryInput
     */
    public function setCountryInput(?ArrayCollection $countryInput): void
    {
        $this->countryInput = $countryInput;
    }

    /**
     * @param CountryInput $countryInput
     */
    public function addCountryInput(CountryInput $countryInput): void
    {
        $this->castInput->add($countryInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getDirectorInput(): ?ArrayCollection
    {
        return $this->directorInput;
    }

    /**
     * @param DirectorInput $directorInput
     */
    public function addDirectorInput(DirectorInput $directorInput): void
    {
        $this->directorInput->add($directorInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getGenreInput(): ?ArrayCollection
    {
        return $this->genreInput;
    }

    /**
     * @param GenreInput $genreInput
     */
    public function addGenreInput(GenreInput $genreInput): void
    {
        $this->genreInput->add($genreInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getImageInput(): ?ArrayCollection
    {
        return $this->imageInput;
    }

    /**
     * @param ImageInput $imageInput
     */
    public function addImageInput(ImageInput $imageInput): void
    {
        $this->imageInput->add($imageInput);
    }

}
