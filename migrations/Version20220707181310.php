<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220707181310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film_poster (image_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_468331F83DA5256D (image_id), INDEX IDX_468331F8567F5183 (film_id), PRIMARY KEY(image_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_banner (image_id INT NOT NULL, film_translation_id INT NOT NULL, INDEX IDX_46F85ED3DA5256D (image_id), INDEX IDX_46F85ED72660D31 (film_translation_id), PRIMARY KEY(image_id, film_translation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_poster ADD CONSTRAINT FK_468331F83DA5256D FOREIGN KEY (image_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE film_poster ADD CONSTRAINT FK_468331F8567F5183 FOREIGN KEY (film_id) REFERENCES `image` (id)');
        $this->addSql('ALTER TABLE film_banner ADD CONSTRAINT FK_46F85ED3DA5256D FOREIGN KEY (image_id) REFERENCES film_by_provider_translation (id)');
        $this->addSql('ALTER TABLE film_banner ADD CONSTRAINT FK_46F85ED72660D31 FOREIGN KEY (film_translation_id) REFERENCES `image` (id)');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245567F5183');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245F92F3E70');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245567F5183 FOREIGN KEY (film_id) REFERENCES country (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245F92F3E70 FOREIGN KEY (country_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA84296D31F');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA8567F5183');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA84296D31F FOREIGN KEY (genre_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA8567F5183 FOREIGN KEY (film_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C53A3123C7');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C5567F5183');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C53A3123C7 FOREIGN KEY (audio_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C5567F5183 FOREIGN KEY (film_id) REFERENCES `audio` (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX IDX_C53D045F567F5183 ON image');
        $this->addSql('ALTER TABLE image DROP film_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE film_poster');
        $this->addSql('DROP TABLE film_banner');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C53A3123C7');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C5567F5183');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C53A3123C7 FOREIGN KEY (audio_id) REFERENCES audio (id)');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C5567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245F92F3E70');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245567F5183');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA84296D31F');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA8567F5183');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA84296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA8567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE `image` ADD film_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_C53D045F567F5183 ON `image` (film_id)');
    }
}
