<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210509151956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, file_system_id INT NOT NULL, user_id INT NOT NULL, media_category VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, temp_path VARCHAR(512) NOT NULL, extension VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, file_size INT NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, INDEX IDX_8C9F36105E9A90D3 (file_system_id), INDEX IDX_8C9F3610A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_download (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, download_key VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C94A0DEDA086DEF4 (download_key), INDEX IDX_C94A0DED93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_system (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, base_path VARCHAR(512) DEFAULT NULL, base_url VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_profile_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6496B9DD454 (user_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_7B42780FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_membership (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, membership_id INT NOT NULL, INDEX IDX_21981469A76ED395 (user_id), INDEX IDX_219814691FB354CD (membership_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, INDEX IDX_472E5446A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_permission_permission (user_permission_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_646B05891057A19A (user_permission_id), INDEX IDX_646B0589FED90CCA (permission_id), PRIMARY KEY(user_permission_id, permission_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, gender_preference VARCHAR(255) DEFAULT NULL, marital_status VARCHAR(255) DEFAULT NULL, dob DATETIME DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, height INT DEFAULT NULL, weight INT DEFAULT NULL, hair_color VARCHAR(255) DEFAULT NULL, eye_color VARCHAR(255) DEFAULT NULL, body_type VARCHAR(255) DEFAULT NULL, ethnicity VARCHAR(255) DEFAULT NULL, sexual_preference VARCHAR(255) DEFAULT NULL, summary VARCHAR(512) DEFAULT NULL, partner_qualities LONGTEXT DEFAULT NULL, interests LONGTEXT DEFAULT NULL, hobbies LONGTEXT DEFAULT NULL, smoking_preference TINYINT(1) DEFAULT NULL, smoking_status VARCHAR(15) DEFAULT NULL, languages VARCHAR(512) DEFAULT NULL, height_unit VARCHAR(15) DEFAULT NULL, weight_unit VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36105E9A90D3 FOREIGN KEY (file_system_id) REFERENCES file_system (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_download ADD CONSTRAINT FK_C94A0DED93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496B9DD454 FOREIGN KEY (user_profile_id) REFERENCES user_profile (id)');
        $this->addSql('ALTER TABLE user_api_token ADD CONSTRAINT FK_7B42780FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_membership ADD CONSTRAINT FK_21981469A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_membership ADD CONSTRAINT FK_219814691FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_permission_permission ADD CONSTRAINT FK_646B05891057A19A FOREIGN KEY (user_permission_id) REFERENCES user_permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission_permission ADD CONSTRAINT FK_646B0589FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_download DROP FOREIGN KEY FK_C94A0DED93CB796C');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36105E9A90D3');
        $this->addSql('ALTER TABLE user_membership DROP FOREIGN KEY FK_219814691FB354CD');
        $this->addSql('ALTER TABLE user_permission_permission DROP FOREIGN KEY FK_646B0589FED90CCA');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE user_api_token DROP FOREIGN KEY FK_7B42780FA76ED395');
        $this->addSql('ALTER TABLE user_membership DROP FOREIGN KEY FK_21981469A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP FOREIGN KEY FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE user_permission_permission DROP FOREIGN KEY FK_646B05891057A19A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496B9DD454');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_download');
        $this->addSql('DROP TABLE file_system');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_api_token');
        $this->addSql('DROP TABLE user_membership');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE user_permission_permission');
        $this->addSql('DROP TABLE user_profile');
    }
}
