<?php

declare(strict_types=1);

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FilmFieldTranslationInput
 * @package App\Dto
 */
class FilmFieldTranslationInput
{

    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 4,
     *      max = 100,
     * )
     */
    private ?string $title;

    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 50,
     *      max = 3000,
     * )
     */
    private ?string $description;

    /**
     * @var ImageInput|null
     */
    private ?ImageInput $bannersInput;

    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\Choice({"EN", "RU", "UK"})
     */
    private ?string $lang;

    public function __construct(
        ?string $title,
        ?string $description,
        ?string $lang
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->lang = $lang;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ImageInput|null
     */
    public function getBannersInput(): ?ImageInput
    {
        return $this->bannersInput;
    }

    /**
     * @param ImageInput|null $bannersInput
     */
    public function setBannersInput(?ImageInput $bannersInput): void
    {
        $this->bannersInput = $bannersInput;
    }

    /**
     * @return string|null
     */
    public function getLang(): ?string
    {
        return $this->lang;
    }

    /**
     * @param string|null $lang
     */
    public function setLang(?string $lang): void
    {
        $this->lang = $lang;
    }
}
