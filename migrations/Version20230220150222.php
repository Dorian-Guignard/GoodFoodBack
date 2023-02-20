<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220150222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE composition (id INT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, food_id INT DEFAULT NULL, unity VARCHAR(20) DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_C7F434759D8A214 (recipe_id), INDEX IDX_C7F4347BA8E87C4 (food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE composition ADD CONSTRAINT FK_C7F434759D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE composition ADD CONSTRAINT FK_C7F4347BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE composition DROP FOREIGN KEY FK_C7F434759D8A214');
        $this->addSql('ALTER TABLE composition DROP FOREIGN KEY FK_C7F4347BA8E87C4');
        $this->addSql('DROP TABLE composition');
    }
}
