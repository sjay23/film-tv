<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719095818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_by_provider ADD poster_uploaded SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE film_by_provider_translation ADD banner_uploaded SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE image ADD file_path VARCHAR(255) DEFAULT NULL, ADD image_name VARCHAR(255) DEFAULT NULL, CHANGE uploaded uploaded SMALLINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_by_provider DROP poster_uploaded');
        $this->addSql('ALTER TABLE film_by_provider_translation DROP banner_uploaded');
        $this->addSql('ALTER TABLE `image` DROP file_path, DROP image_name, CHANGE uploaded uploaded TINYINT(1) NOT NULL');
    }
}
