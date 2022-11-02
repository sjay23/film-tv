<?php

namespace App\Tests;

use App\DTO\ImageInput;
use App\Service\Parsers\Megogo\FilmFieldTranslateService;
use App\Service\Parsers\Megogo\FilmImageService;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;

class ParseFilmTranslateTest extends TestUnitMain
{
    public const LINK_EN = 'https://megogo.net/en/view/7585835-crypto.html';
    public const LINK_RU = 'https://megogo.net/ru/view/7585835-crypto.html';

    /**
     * @throws GuzzleException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlerEn = $this->traitClass->getCrawlerByLink(self::LINK_EN);
        $this->crawlerRu = $this->traitClass->getCrawlerByLink(self::LINK_RU);
    }

    public function getService(): FilmFieldTranslateService
    {
        return new FilmFieldTranslateService($this->validator);
    }

    public function testParseFilmTranslateTitle()
    {
        $titleEn = $this->getService()->parseTitleTranslate($this->crawlerEn);
        $titleRu = $this->getService()->parseTitleTranslate($this->crawlerRu);
        $this->assertEquals("Crypto", $titleEn);
        $this->assertEquals("Крипто", $titleRu);
    }

    /**
     * @throws ReflectionException
     */
    public function testParseFilmTranslateBannerLink()
    {
        $bannerEn = $this->getService()->parseBannerTranslate($this->crawlerEn);
        $bannerRU = $this->getService()->parseBannerTranslate($this->crawlerRu);

        $this->assertEquals("https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423", $bannerEn->getLink());
        $this->assertEquals("https://s6.vcdn.biz/static/f/5123281461/image.jpg/pt/r300x423", $bannerRU->getLink());
    }

    public function testParseFilmTranslateDescription()
    {
        $descriptionEn = $this->getService()->parseDescriptionTranslate($this->crawlerEn);
        $descriptionRu = $this->getService()->parseDescriptionTranslate($this->crawlerRu);
        $this->assertEquals("Martin works for a bank in the department that deals with illegal financial transactions. One day, while on a business trip to his native provincial town, the protagonist unexpectedly faces illegal sales of paintings for crypt currency in the local gallery.", $descriptionEn);
        $this->assertEquals("Талантливый банковский аналитик Мартин Дюран, успешно раскрывающий аферы по отмыванию денег, оказывается в опале у руководства за свое усердие и порядочность. В наказание Мартин получает принудительную высылку на новое место работы — в банковское отделение маленькой провинции Эльба на юге штата Нью-Йорк. Мартин в расстроенных чувствах отправляется в Эльбу. В этом городке он родился и вырос, а после смерти матери сбежал в Нью-Йорк. Отношения с отцом и братом, едва справляющимся на семейном ранчо, давно разрушены. Тем не менее, в забытой богом глубинке Мартину удается выйти на след больших мафиозных денег, которые отмываются через картинную галерею. Не привыкший отступать, он берется раскручивать это дело, ставя под угрозу свою разрозненную семью. Криминальный финансовый триллер Крипто поставлен американским режиссером Джоном Сталбергом при финансовой поддержке кинопроизводственной компании Yale Productions. Съемки проходили на Манхэттене в Нью-Йорке. Вступительная сцена была снята в южной части штата Нью-Йорк — городе Эльбе, бывшей древней общине. На одну из главных ролей был утвержден голливудский актер, сценарист и продюсер Курт Рассел. Премьера состоялась 12 апреля 2019 года в США.Талантливый банковский аналитик Мартин Дюран, успешно раскрывающий аферы по отмыванию денег, оказывается в опале у руководства за свое усердие и порядочность. В наказание Мартин получает принудительную высылку на новое место работы — в банковское отделение маленькой провинции Эльба на юге штата Нью-Йорк. Мартин в расстроенных чувствах отправляется в Эльбу. В этом городке он родился и вырос, а посСвернутьПодробнее", $descriptionRu);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetImageInput()
    {
        $object = $this->getService();
        $imageInput = $this->invokeMethod(
            $object,
            'getImageInput',
            ['https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423']
        );
        $this->assertInstanceOf(ImageInput::class, $imageInput);
        $this->assertEquals(
            'https://s9.vcdn.biz/static/f/5123281761/image.jpg/pt/r300x423',
            $imageInput->getLink()
        );
    }
}
