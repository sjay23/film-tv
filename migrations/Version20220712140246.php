<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712140246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command_task ADD CONSTRAINT FK_F3D475A8A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('CREATE INDEX IDX_F3D475A8A53A8AA ON command_task (provider_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE command_task DROP FOREIGN KEY FK_F3D475A8A53A8AA');
        $this->addSql('DROP INDEX IDX_F3D475A8A53A8AA ON command_task');
    }
}
