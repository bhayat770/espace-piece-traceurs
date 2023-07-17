<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230714115549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE traceurs_tag (traceurs_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_1079531A2345A848 (traceurs_id), INDEX IDX_1079531ABAD26311 (tag_id), PRIMARY KEY(traceurs_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traceurs_tag ADD CONSTRAINT FK_1079531A2345A848 FOREIGN KEY (traceurs_id) REFERENCES traceurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE traceurs_tag ADD CONSTRAINT FK_1079531ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE traceurs_tag DROP FOREIGN KEY FK_1079531A2345A848');
        $this->addSql('ALTER TABLE traceurs_tag DROP FOREIGN KEY FK_1079531ABAD26311');
        $this->addSql('DROP TABLE traceurs_tag');

    }
}
