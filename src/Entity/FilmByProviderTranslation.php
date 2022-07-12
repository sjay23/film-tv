<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity()
 */
class FilmByProviderTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private ?string $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", inversedBy="filmBanner")
     * @ORM\JoinTable(name="film_banner",
     *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_translation_id", referencedColumnName="id")}
     *      )
     */
    private ?Collection $banner;

    /**
     * @ORM\Column(type="string", length=5000, nullable="true")
     */
    private ?string $description;

    public function __construct()
    {
        $this->banner = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
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
     * @return ArrayCollection|Collection|null
     */
    public function getBanner()
    {
        return $this->banner;
    }

    public function setBanner(Image $banner): self
    {
        if (!$this->banner->contains($banner)) {
            $this->banner[] = $banner;
        }
        return $this;
    }
}
