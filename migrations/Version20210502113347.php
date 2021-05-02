<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210502113347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_7B42780FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission_permission (user_permission_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_646B05891057A19A (user_permission_id), INDEX IDX_646B0589FED90CCA (permission_id), PRIMARY KEY(user_permission_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_api_token ADD CONSTRAINT FK_7B42780FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_permission_permission ADD CONSTRAINT FK_646B05891057A19A FOREIGN KEY (user_permission_id) REFERENCES user_permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission_permission ADD CONSTRAINT FK_646B0589FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_permission_permission DROP FOREIGN KEY FK_646B0589FED90CCA');
        $this->addSql('ALTER TABLE user_permission_permission DROP FOREIGN KEY FK_646B05891057A19A');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE user_api_token');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE user_permission_permission');
    }
}
