<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704164616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film_by_provider_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(500) NOT NULL, description VARCHAR(5000) DEFAULT NULL, locale VARCHAR(5) NOT NULL, UNIQUE INDEX UNIQ_B9A12F022B36786B (title), INDEX IDX_B9A12F022C2AC5D3 (translatable_id), UNIQUE INDEX film_by_provider_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_by_provider_translation ADD CONSTRAINT FK_B9A12F022C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_4BD63F1A2B36786B ON film_by_provider');
        $this->addSql('ALTER TABLE film_by_provider DROP title, DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE film_by_provider_translation');
        $this->addSql('ALTER TABLE film_by_provider ADD title VARCHAR(500) NOT NULL, ADD description VARCHAR(5000) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4BD63F1A2B36786B ON film_by_provider (title)');
    }
}
