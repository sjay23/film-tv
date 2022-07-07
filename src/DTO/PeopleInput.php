<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PeopleInput
 * @package App\Dto
 */
class PeopleInput
{
    /**
     * @var string|null
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     * )
     */
    private ?string $name;

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
        ?string $name,
        ?string $link
    )
    {
        $this->name = $name;
        $this->link = $link;
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
