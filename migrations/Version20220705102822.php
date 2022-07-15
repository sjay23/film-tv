<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705102822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film_director (film_id INT NOT NULL, director_id INT NOT NULL, INDEX IDX_BC171C99567F5183 (film_id), INDEX IDX_BC171C99899FB366 (director_id), PRIMARY KEY(film_id, director_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_director ADD CONSTRAINT FK_BC171C99567F5183 FOREIGN KEY (film_id) REFERENCES film_by_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_director ADD CONSTRAINT FK_BC171C99899FB366 FOREIGN KEY (director_id) REFERENCES `people` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE people DROP FOREIGN KEY FK_28166A26899FB366');
        $this->addSql('DROP INDEX IDX_28166A26899FB366 ON people');
        $this->addSql('ALTER TABLE people DROP director_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE film_director');
        $this->addSql('ALTER TABLE `people` ADD director_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `people` ADD CONSTRAINT FK_28166A26899FB366 FOREIGN KEY (director_id) REFERENCES film_by_provider (id)');
        $this->addSql('CREATE INDEX IDX_28166A26899FB366 ON `people` (director_id)');
    }
}
