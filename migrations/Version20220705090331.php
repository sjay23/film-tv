<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705090331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `audio` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_audio (audio_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_8111C8C53A3123C7 (audio_id), INDEX IDX_8111C8C5567F5183 (film_id), PRIMARY KEY(audio_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_country (country_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_B3CDD245F92F3E70 (country_id), INDEX IDX_B3CDD245567F5183 (film_id), PRIMARY KEY(country_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_by_provider (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, link VARCHAR(500) NOT NULL, year SMALLINT DEFAULT NULL, rating NUMERIC(4, 2) DEFAULT NULL, age VARCHAR(5) DEFAULT NULL, duration INT DEFAULT NULL, UNIQUE INDEX UNIQ_4BD63F1A36AC99F1 (link), INDEX IDX_4BD63F1AA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_actor (film_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_DD19A8A9567F5183 (film_id), INDEX IDX_DD19A8A910DAF24A (actor_id), PRIMARY KEY(film_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_by_provider_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(500) NOT NULL, description VARCHAR(5000) DEFAULT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_B9A12F022B36786B (title), INDEX IDX_B9A12F022C2AC5D3 (translatable_id), UNIQUE INDEX film_by_provider_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_genre (genre_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_1A3CCDA84296D31F (genre_id), INDEX IDX_1A3CCDA8567F5183 (film_id), PRIMARY KEY(genre_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `image` (id INT AUTO_INCREMENT NOT NULL, film_id INT DEFAULT NULL, link VARCHAR(500) NOT NULL, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, uploaded TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C53D045F36AC99F1 (link), INDEX IDX_C53D045F567F5183 (film_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `people` (id INT AUTO_INCREMENT NOT NULL, director_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(500) NOT NULL, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, uploaded TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_28166A2636AC99F1 (link), INDEX IDX_28166A26899FB366 (director_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C53A3123C7 FOREIGN KEY (audio_id) REFERENCES `audio` (id)');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C5567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE film_country ADD CONSTRAINT FK_B3CDD245567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE film_by_provider ADD CONSTRAINT FK_4BD63F1AA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE film_actor ADD CONSTRAINT FK_DD19A8A9567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_actor ADD CONSTRAINT FK_DD19A8A910DAF24A FOREIGN KEY (actor_id) REFERENCES `people` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_by_provider_translation ADD CONSTRAINT FK_B9A12F022C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA84296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA8567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE `image` ADD CONSTRAINT FK_C53D045F567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id)');
        $this->addSql('ALTER TABLE `people` ADD CONSTRAINT FK_28166A26899FB366 FOREIGN KEY (director_id) REFERENCES film_by_provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C53A3123C7');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245F92F3E70');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C5567F5183');
        $this->addSql('ALTER TABLE film_country DROP FOREIGN KEY FK_B3CDD245567F5183');
        $this->addSql('ALTER TABLE film_actor DROP FOREIGN KEY FK_DD19A8A9567F5183');
        $this->addSql('ALTER TABLE film_by_provider_translation DROP FOREIGN KEY FK_B9A12F022C2AC5D3');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA8567F5183');
        $this->addSql('ALTER TABLE `image` DROP FOREIGN KEY FK_C53D045F567F5183');
        $this->addSql('ALTER TABLE `people` DROP FOREIGN KEY FK_28166A26899FB366');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA84296D31F');
        $this->addSql('ALTER TABLE film_actor DROP FOREIGN KEY FK_DD19A8A910DAF24A');
        $this->addSql('ALTER TABLE film_by_provider DROP FOREIGN KEY FK_4BD63F1AA53A8AA');
        $this->addSql('DROP TABLE `audio`');
        $this->addSql('DROP TABLE film_audio');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE film_country');
        $this->addSql('DROP TABLE film_by_provider');
        $this->addSql('DROP TABLE film_actor');
        $this->addSql('DROP TABLE film_by_provider_translation');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE film_genre');
        $this->addSql('DROP TABLE `image`');
        $this->addSql('DROP TABLE `people`');
        $this->addSql('DROP TABLE provider');
    }
}
