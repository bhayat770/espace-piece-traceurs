<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719083158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_details CHANGE illustration illustration VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_845CA2C14584665A ON order_details (product_id)');
        $this->addSql('ALTER TABLE traceurs ADD serie VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C14584665A');
        $this->addSql('DROP INDEX IDX_845CA2C14584665A ON order_details');
        $this->addSql('ALTER TABLE order_details CHANGE illustration illustration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE traceurs DROP serie');
    }
}
