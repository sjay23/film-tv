<?php

namespace App\Service;

use App\DTO\FilmFieldTranslationInput;
use App\DTO\FilmInput;
use App\Entity\FilmByProvider;
use App\Repository\FilmByProviderRepository;
use App\Repository\ProviderRepository;
use App\Service\AudioService;
use App\Service\GenreService;
use App\Service\PeopleService;
use App\Service\CountryService;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class FilmByProviderService
 */
class FilmByProviderService
{

    /**
     * @var GenreService
     */
    private GenreService $genreService;

    /**
     * @var ImageService
     */
    private ImageService $imageService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var AudioService
     */
    private AudioService $audioService;

    /**
     * @var PeopleService
     */
    private PeopleService $peopleService;

    /**
     * @var CountryService
     */
    private CountryService $countryService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param GenreService $genreService
     * @param ImageService $imageService
     * @param AudioService $audioService
     * @param CountryService $countryService
     * @param PeopleService $peopleService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GenreService $genreService,
        ImageService $imageService,
        AudioService $audioService,
        CountryService $countryService,
        PeopleService $peopleService
    )
    {
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
        $this->genreService = $genreService;
        $this->audioService = $audioService;
        $this->countryService = $countryService;
        $this->peopleService = $peopleService;
    }

    /**
     * @param FilmInput $filmInput
     * @return FilmByProvider
     */
    public function addFilmByProvider(FilmInput $filmInput): FilmByProvider
    {
       $film= new FilmByProvider();
       $filmFieldsTranslationInput = $filmInput->getFilmFieldsTranslationInput();
       $film->setYear($filmInput->getYears());
       $film->setProvider($filmInput->getProvider());
       foreach ($filmFieldsTranslationInput as $filmFieldsTranslationInputByLang){
            /**
             * @var FilmFieldTranslationInput $filmFieldsTranslationInputByLang
             */
            $film->translate($filmFieldsTranslationInputByLang->getLang())->setTitle($filmFieldsTranslationInputByLang->getTitle());
            $film->translate($filmFieldsTranslationInputByLang->getLang())->setDescription($filmFieldsTranslationInputByLang->getDescription());
            $banner = $this->imageService->getImage($filmFieldsTranslationInputByLang->getBannersInput());
            $film->translate($filmFieldsTranslationInputByLang->getLang())->setBanner($banner);
       }

       $actors = [];
        foreach ($filmInput->getCastsInput() as $peopleInput) {
            $actor = $this->peopleService->getPeople($peopleInput);
            $actors[] = $actor;
            $film->setActor($actor);
        }

        foreach ($filmInput->getDirectorsInput() as $peopleInput) {
            $director = $this->peopleService->checkActors($peopleInput, $actors);
            if ($director === null) {
                $director = $this->peopleService->getPeople($peopleInput);
            }
            $film->setDirector($director);
        }

        foreach ($filmInput->getCountriesInput() as $countryInput) {
            $film->setCountry($this->countryService->getCountry($countryInput));
        }

        foreach ($filmInput->getGenresInput() as $genreInput) {
            $film->setGenre($this->genreService->getGenre($genreInput));
}

        foreach ($filmInput->getAudiosInput() as $audioInput) {
            $film->setAudio($this->audioService->getAudio($audioInput));
        }

        foreach ($filmInput->getImagesInput() as $imageInput) {
            $film->setPoster($this->imageService->getImage($imageInput));
        }

        $film->setAge($filmInput->getAge());
        $film->setDuration($filmInput->getDuration());
        $film->setLink($filmInput->getLink());
        $film->setMovieId($filmInput->getMovieId());
        $film->setRating($filmInput->getRating());

        $this->entityManager->persist($film);
        $film->mergeNewTranslations();
        $this->entityManager->flush();

        return $film;
    }


}
