<?php

declare(strict_types=1);

namespace App\DTO;

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
     * @var string|null
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 150,
     * )
     */
    private ?string $banner;

    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\Choice({"EN", "RU", "UK"})
     */
    private ?string $lang;

    public function __construct(
        ?string $title,
        ?string $description,
        ?string $banner,
        ?string $lang
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->banner = $banner;
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
     * @return string|null
     */
    public function getBanner(): ?string
    {
        return $this->banner;
    }

    /**
     * @param string|null $banner
     */
    public function setBanner(?string $banner): void
    {
        $this->banner = $banner;
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
