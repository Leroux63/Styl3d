<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220930130025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE name title VARCHAR(100) NOT NULL, CHANGE content item LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE article_category CHANGE name name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE product DROP price');
        $this->addSql('ALTER TABLE user DROP username');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE title name VARCHAR(100) NOT NULL, CHANGE item content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE article_category CHANGE name name VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE product ADD price DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(100) NOT NULL');
    }
}
