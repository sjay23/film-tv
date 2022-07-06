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
     * @var string|null
     */
    private ?string $age;

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
    private ?ArrayCollection $genresInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $audiosInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $castsInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $countriesInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $imagesInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $directorsInput;

    /**
     * @var ArrayCollection|null
     */
    private ?ArrayCollection $filmFieldsTranslationInput;

    public function __construct()
    {
        $this->filmFieldsTranslationInput = new ArrayCollection();
        $this->countriesInput = new ArrayCollection();
        $this->directorsInput = new ArrayCollection();
        $this->castsInput = new ArrayCollection();
        $this->audiosInput = new ArrayCollection();
        $this->imagesInput = new ArrayCollection();
        $this->genresInput = new ArrayCollection();

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
     * @return string|null
     */
    public function getAge(): ?string
    {
        return $this->age;
    }

    /**
     * @param string|null $age
     */
    public function setAge(?string $age): void
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
    public function getFilmFieldsTranslationInput(): ?ArrayCollection
    {
        return $this->filmFieldsTranslationInput;
    }

    /**
     * @param ArrayCollection|null $filmFieldsTranslationInput
     */
    public function setFilmFieldsTranslationInput(?ArrayCollection $filmFieldsTranslationInput): void
    {
        $this->filmFieldsTranslationInput = $filmFieldsTranslationInput;
    }

    /**
     * @param FilmFieldTranslationInput $filmFieldTranslationInput
     */
    public function addFilmFieldTranslationInput(FilmFieldTranslationInput $filmFieldTranslationInput): void
    {
        $this->filmFieldsTranslationInput->add($filmFieldTranslationInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getAudiosInput(): ?ArrayCollection
    {
        return $this->audiosInput;
    }

    /**
     * @param ArrayCollection|null $audiosInput
     */
    public function setAudiosInput(?ArrayCollection $audiosInput): void
    {
        $this->audiosInput = $audiosInput;
    }

    /**
     * @param AudioInput $audioInput
     */
    public function addAudioInput(AudioInput $audioInput): void
    {
        $this->audiosInput->add($audioInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getCastsInput(): ?ArrayCollection
    {
        return $this->castsInput;
    }

    /**
     * @param ArrayCollection|null $castsInput
     */
    public function setCastsInput(?ArrayCollection $castsInput): void
    {
        $this->castsInput = $castsInput;
    }

    /**
     * @param CastInput $castInput
     */
    public function addCastInput(CastInput $castInput): void
    {
        $this->castsInput->add($castInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getCountriesInput(): ?ArrayCollection
    {
        return $this->countriesInput;
    }

    /**
     * @param ArrayCollection|null $countriesInput
     */
    public function setCountriesInput(?ArrayCollection $countriesInput): void
    {
        $this->countriesInput = $countriesInput;
    }

    /**
     * @param CountryInput $countryInput
     */
    public function addCountryInput(CountryInput $countryInput): void
    {
        $this->castsInput->add($countryInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getDirectorsInput(): ?ArrayCollection
    {
        return $this->directorsInput;
    }

    /**
     * @param ArrayCollection|null $directorsInput
     */
    public function setDirectorsInput(?ArrayCollection $directorsInput): void
    {
        $this->directorsInput = $directorsInput;
    }

    /**
     * @param DirectorInput $directorInput
     */
    public function addDirectorInput(DirectorInput $directorInput): void
    {
        $this->directorsInput->add($directorInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getGenresInput(): ?ArrayCollection
    {
        return $this->genresInput;
    }

    /**
     * @param ArrayCollection|null $genresInput
     */
    public function setGenresInput(?ArrayCollection $genresInput): void
    {
        $this->genresInput = $genresInput;
    }

    /**
     * @param GenreInput $genreInput
     */
    public function addGenreInput(GenreInput $genreInput): void
    {
        $this->genresInput->add($genreInput);
    }

    /**
     * @return ArrayCollection|null
     */
    public function getImagesInput(): ?ArrayCollection
    {
        return $this->imagesInput;
    }

    /**
     * @param ArrayCollection|null $imagesInput
     */
    public function setImagesInput(?ArrayCollection $imagesInput): void
    {
        $this->imagesInput = $imagesInput;
    }

    /**
     * @param ImageInput $imageInput
     */
    public function addImageInput(ImageInput $imageInput): void
    {
        $this->imagesInput->add($imageInput);
    }

}
