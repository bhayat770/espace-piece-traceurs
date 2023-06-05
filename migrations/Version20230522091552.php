<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522091552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD pays_drapeau_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F08168A540851 FOREIGN KEY (pays_drapeau_id) REFERENCES pays (id)');
        $this->addSql('CREATE INDEX IDX_C35F08168A540851 ON adresse (pays_drapeau_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F08168A540851');
        $this->addSql('DROP INDEX IDX_C35F08168A540851 ON adresse');
        $this->addSql('ALTER TABLE adresse DROP pays_drapeau_id');
    }
}
