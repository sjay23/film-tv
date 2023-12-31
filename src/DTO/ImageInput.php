<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageInput
 * @package App\Dto
 */
class ImageInput
{
    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 150,
     * )
     */
    private ?string $link;


    public function __construct(
        ?string $link
    ) {
        $this->link = $link;
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
}
