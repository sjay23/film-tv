<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704131416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `audio` (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_187D3695D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_audio (audio_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_8111C8C53A3123C7 (audio_id), INDEX IDX_8111C8C5567F5183 (film_id), PRIMARY KEY(audio_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `film_by_provider` (id INT AUTO_INCREMENT NOT NULL, provider_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(500) NOT NULL, link VARCHAR(500) NOT NULL, description VARCHAR(5000) DEFAULT NULL, year SMALLINT DEFAULT NULL, rating NUMERIC(4, 2) DEFAULT NULL, country VARCHAR(30) DEFAULT NULL, age VARCHAR(5) DEFAULT NULL, duration INT DEFAULT NULL, UNIQUE INDEX UNIQ_4BD63F1AD17F50A6 (uuid), UNIQUE INDEX UNIQ_4BD63F1A2B36786B (title), UNIQUE INDEX UNIQ_4BD63F1A36AC99F1 (link), INDEX IDX_4BD63F1AA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_actor (film_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_DD19A8A9567F5183 (film_id), INDEX IDX_DD19A8A910DAF24A (actor_id), PRIMARY KEY(film_id, actor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `image` (id INT AUTO_INCREMENT NOT NULL, film_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', link VARCHAR(500) NOT NULL, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, uploaded TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C53D045FD17F50A6 (uuid), UNIQUE INDEX UNIQ_C53D045F36AC99F1 (link), INDEX IDX_C53D045F567F5183 (film_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `people` (id INT AUTO_INCREMENT NOT NULL, director_id INT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, link VARCHAR(500) NOT NULL, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP, uploaded TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_28166A26D17F50A6 (uuid), UNIQUE INDEX UNIQ_28166A2636AC99F1 (link), INDEX IDX_28166A26899FB366 (director_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_92C4739CD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C53A3123C7 FOREIGN KEY (audio_id) REFERENCES `audio` (id)');
        $this->addSql('ALTER TABLE film_audio ADD CONSTRAINT FK_8111C8C5567F5183 FOREIGN KEY (film_id) REFERENCES `film_by_provider` (id)');
        $this->addSql('ALTER TABLE `film_by_provider` ADD CONSTRAINT FK_4BD63F1AA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE film_actor ADD CONSTRAINT FK_DD19A8A9567F5183 FOREIGN KEY (film_id) REFERENCES `film_by_provider` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_actor ADD CONSTRAINT FK_DD19A8A910DAF24A FOREIGN KEY (actor_id) REFERENCES `people` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `image` ADD CONSTRAINT FK_C53D045F567F5183 FOREIGN KEY (film_id) REFERENCES `film_by_provider` (id)');
        $this->addSql('ALTER TABLE `people` ADD CONSTRAINT FK_28166A26899FB366 FOREIGN KEY (director_id) REFERENCES `film_by_provider` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C53A3123C7');
        $this->addSql('ALTER TABLE film_audio DROP FOREIGN KEY FK_8111C8C5567F5183');
        $this->addSql('ALTER TABLE film_actor DROP FOREIGN KEY FK_DD19A8A9567F5183');
        $this->addSql('ALTER TABLE `image` DROP FOREIGN KEY FK_C53D045F567F5183');
        $this->addSql('ALTER TABLE `people` DROP FOREIGN KEY FK_28166A26899FB366');
        $this->addSql('ALTER TABLE film_actor DROP FOREIGN KEY FK_DD19A8A910DAF24A');
        $this->addSql('ALTER TABLE `film_by_provider` DROP FOREIGN KEY FK_4BD63F1AA53A8AA');
        $this->addSql('DROP TABLE `audio`');
        $this->addSql('DROP TABLE film_audio');
        $this->addSql('DROP TABLE `film_by_provider`');
        $this->addSql('DROP TABLE film_actor');
        $this->addSql('DROP TABLE `image`');
        $this->addSql('DROP TABLE `people`');
        $this->addSql('DROP TABLE provider');
    }
}
