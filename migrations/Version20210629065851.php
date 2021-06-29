<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629065851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accessoire (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_8FD026AC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animaux (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, race VARCHAR(255) NOT NULL, poids DOUBLE PRECISION NOT NULL, age INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_9ABE194DC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, INDEX IDX_23A0E6660BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dons (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, montant DOUBLE PRECISION NOT NULL, message VARCHAR(255) DEFAULT NULL, INDEX IDX_E4F955FAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nourriture (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_7447E613C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_role (utilisateur_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_9EE8E650FB88E14F (utilisateur_id), INDEX IDX_9EE8E650D60322AC (role_id), PRIMARY KEY(utilisateur_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accessoire ADD CONSTRAINT FK_8FD026AC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE animaux ADD CONSTRAINT FK_9ABE194DC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6660BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FAA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE nourriture ADD CONSTRAINT FK_7447E613C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_role ADD CONSTRAINT FK_9EE8E650D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650D60322AC');
        $this->addSql('ALTER TABLE accessoire DROP FOREIGN KEY FK_8FD026AC54C8C93');
        $this->addSql('ALTER TABLE animaux DROP FOREIGN KEY FK_9ABE194DC54C8C93');
        $this->addSql('ALTER TABLE nourriture DROP FOREIGN KEY FK_7447E613C54C8C93');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6660BB6FE6');
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FAA76ED395');
        $this->addSql('ALTER TABLE utilisateur_role DROP FOREIGN KEY FK_9EE8E650FB88E14F');
        $this->addSql('DROP TABLE accessoire');
        $this->addSql('DROP TABLE animaux');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE dons');
        $this->addSql('DROP TABLE nourriture');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_role');
    }
}
