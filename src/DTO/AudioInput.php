<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GenreInput
 * @package App\Dto
 */
class AudioInput
{
    /**
     * @var string|null
     * @Assert\NotNull
     */
    private ?string $name;


    public function __construct(
        ?string $name
    )
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }


}
