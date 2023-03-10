<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309142816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE steps (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, name INT NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_34220A7259D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE steps ADD CONSTRAINT FK_34220A7259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE category DROP picture');
        $this->addSql('ALTER TABLE composition CHANGE quantity quantity VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE food DROP picture');
        $this->addSql('ALTER TABLE user ADD name_image VARCHAR(255) DEFAULT NULL, ADD name_user VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649CA250C3E ON user (name_user)');
        $this->addSql('ALTER TABLE virtue DROP picture');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE steps DROP FOREIGN KEY FK_34220A7259D8A214');
        $this->addSql('DROP TABLE steps');
        $this->addSql('ALTER TABLE composition CHANGE quantity quantity VARCHAR(10) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649CA250C3E ON user');
        $this->addSql('ALTER TABLE user DROP name_image, DROP name_user');
        $this->addSql('ALTER TABLE food ADD picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE virtue ADD picture VARCHAR(255) DEFAULT NULL');
    }
}
