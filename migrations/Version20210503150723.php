<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503150723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, media_category VARCHAR(255) NOT NULL, media_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(512) NOT NULL, full_path VARCHAR(512) NOT NULL, extension VARCHAR(255) NOT NULL, file_type VARCHAR(255) NOT NULL, file_size INT NOT NULL, file_system VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_created DATETIME NOT NULL, mime_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_download (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, download_key VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C94A0DEDA086DEF4 (download_key), INDEX IDX_C94A0DED93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE file_download ADD CONSTRAINT FK_C94A0DED93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_download DROP FOREIGN KEY FK_C94A0DED93CB796C');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_download');
    }
}
