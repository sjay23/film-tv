<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Provider;
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
     */
    private ?string $title;

    /**
     * @var string|null
     * @Assert\NotNull
     */
    private ?string $description;

    /**
     * @var string|null
     * @Assert\NotNull
     */
    private ?string $banner;

    /**
     * @var string|null
     * @Assert\NotNull
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
