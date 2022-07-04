<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220704135339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_187D3695D17F50A6 ON audio');
        $this->addSql('ALTER TABLE audio DROP uuid');
        $this->addSql('DROP INDEX UNIQ_4BD63F1AD17F50A6 ON film_by_provider');
        $this->addSql('ALTER TABLE film_by_provider DROP uuid');
        $this->addSql('DROP INDEX UNIQ_C53D045FD17F50A6 ON image');
        $this->addSql('ALTER TABLE image DROP uuid');
        $this->addSql('DROP INDEX UNIQ_28166A26D17F50A6 ON people');
        $this->addSql('ALTER TABLE people DROP uuid');
        $this->addSql('DROP INDEX UNIQ_92C4739CD17F50A6 ON provider');
        $this->addSql('ALTER TABLE provider DROP uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `audio` ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_187D3695D17F50A6 ON `audio` (uuid)');
        $this->addSql('ALTER TABLE `film_by_provider` ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4BD63F1AD17F50A6 ON `film_by_provider` (uuid)');
        $this->addSql('ALTER TABLE `image` ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FD17F50A6 ON `image` (uuid)');
        $this->addSql('ALTER TABLE `people` ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_28166A26D17F50A6 ON `people` (uuid)');
        $this->addSql('ALTER TABLE provider ADD uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92C4739CD17F50A6 ON provider (uuid)');
    }
}
