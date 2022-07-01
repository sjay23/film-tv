<?php

namespace App\Entity;

use App\Repository\AudioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AudioRepository::class)]
class Audio
{
    public const English = 0;
    public const Russian = 1;
    public const Ukrainian = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'smallint', length: 255,options:[ "default" => 0])]
    private int $audioLanguage= self::English;


    #[ORM\OneToMany(targetEntity:"App\Entity\FilmByPovider", mappedBy:"audio")]
    private $films;

    public function __construct()
    {
        $this->films= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAudioLanguage()
    {
        return $this->audioLanguage;
    }

    /**
     * @param mixed $audioLanguage
     */
    public function setAudioLanguage($audioLanguage): void
    {
        $this->audioLanguage = $audioLanguage;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilms(): ArrayCollection
    {
        return $this->films;
    }

    /**
     * @param ArrayCollection $films
     */
    public function setFilms(ArrayCollection $films): void
    {
        $this->films = $films;
    }



}
