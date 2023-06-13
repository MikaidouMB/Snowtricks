<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230610190124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6AB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, trick_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_validated TINYINT(1) DEFAULT NULL, INDEX IDX_B6BD307FA76ED395 (user_id), INDEX IDX_B6BD307FB281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name_figure VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E1D902C1292F2037 (name_figure), INDEX IDX_E1D902C1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trick_category (trick_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_639F9D7EB281BE2E (trick_id), INDEX IDX_639F9D7E12469DE2 (category_id), PRIMARY KEY(trick_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', account_must_be_verified_before DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', registration_token VARCHAR(255) DEFAULT NULL, forgot_password_token VARCHAR(255) DEFAULT NULL, forgot_password_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', forgot_password_token_must_be_verified_before DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', forgot_password_token_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', account_verified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', username VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE videos (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_29AA6432B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7EB281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_category ADD CONSTRAINT FK_639F9D7E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432B281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7E12469DE2');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB281BE2E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB281BE2E');
        $this->addSql('ALTER TABLE trick_category DROP FOREIGN KEY FK_639F9D7EB281BE2E');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432B281BE2E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C1A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE tricks');
        $this->addSql('DROP TABLE trick_category');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE videos');
    }
}
