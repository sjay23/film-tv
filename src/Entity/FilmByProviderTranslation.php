<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
#[ApiResource(
    itemOperations: [
        'get',
        'delete'
    ],
    denormalizationContext: [
        'groups' => [
            'get',
            'post',
        ]
    ],
    normalizationContext: [
        'groups' => [
            'get',
            'post',
        ]
    ]
)]
class FilmByProviderTranslation implements TranslationInterface
{
    use TranslationTrait;

    public const UPLOAD = 1;
    public const NO_UPLOAD = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post", "get"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=500)
     * @Groups({"post", "get"})
     */
    private ?string $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", inversedBy="filmBanner")
     * @ORM\JoinTable(name="film_banner",
     *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="film_translation_id", referencedColumnName="id")}
     *      )
     * @Groups({"post", "get"})
     */
    private ?Collection $banner;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     * @Groups({"post", "get"})
     */
    private int $bannerUploaded = self::NO_UPLOAD;

    /**
     * @ORM\Column(type="string", length=5000, nullable="true")
     * @Groups({"post", "get"})
     */
    private ?string $description;

    public function __construct()
    {
        $this->bannerUploaded = self::NO_UPLOAD;
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

    /**
     * @return int
     */
    public function getBannerUploaded(): int
    {
        return $this->bannerUploaded;
    }

    /**
     * @param int $bannerUploaded
     */
    public function setBannerUploaded(int $bannerUploaded): void
    {
        $this->bannerUploaded = $bannerUploaded;
    }

}
