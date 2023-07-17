<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230714105754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE traceurs (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, photo VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE traceurs_product (traceurs_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2DEB729C2345A848 (traceurs_id), INDEX IDX_2DEB729C4584665A (product_id), PRIMARY KEY(traceurs_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traceurs_product ADD CONSTRAINT FK_2DEB729C2345A848 FOREIGN KEY (traceurs_id) REFERENCES traceurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE traceurs_product ADD CONSTRAINT FK_2DEB729C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE traceurs_product DROP FOREIGN KEY FK_2DEB729C2345A848');
        $this->addSql('ALTER TABLE traceurs_product DROP FOREIGN KEY FK_2DEB729C4584665A');
        $this->addSql('DROP TABLE traceurs');
        $this->addSql('DROP TABLE traceurs_product');
    }
}
