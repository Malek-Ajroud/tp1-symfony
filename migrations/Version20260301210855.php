<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260301210855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    // Ensure categorie has an id before adding a foreign key referencing it
    // Add id column and primary key (do not drop other columns here to avoid errors if they were removed manually)
    $this->addSql('ALTER TABLE categorie ADD id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
    // article.categorie_id already exists in the database; just add index and FK
    $this->addSql('CREATE INDEX IDX_23A0E66BCF5E72D ON article (categorie_id)');
    $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66BCF5E72D');
        $this->addSql('DROP INDEX IDX_23A0E66BCF5E72D ON article');
        $this->addSql('ALTER TABLE article DROP categorie_id');
        // revert categorie to previous columns
        $this->addSql('ALTER TABLE categorie MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE categorie ADD nom VARCHAR(100) NOT NULL, ADD description TEXT DEFAULT NULL, DROP id, DROP PRIMARY KEY');
    }
}
