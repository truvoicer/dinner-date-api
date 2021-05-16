<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210516180555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_media_collection (id INT AUTO_INCREMENT NOT NULL, media_collection_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, INDEX IDX_8B7DE97BB52E685C (media_collection_id), INDEX IDX_8B7DE97BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_media_collection_file (user_media_collection_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_709123E49FED072E (user_media_collection_id), INDEX IDX_709123E493CB796C (file_id), PRIMARY KEY(user_media_collection_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_media_collection ADD CONSTRAINT FK_8B7DE97BB52E685C FOREIGN KEY (media_collection_id) REFERENCES media_collection (id)');
        $this->addSql('ALTER TABLE user_media_collection ADD CONSTRAINT FK_8B7DE97BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_media_collection_file ADD CONSTRAINT FK_709123E49FED072E FOREIGN KEY (user_media_collection_id) REFERENCES user_media_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_media_collection_file ADD CONSTRAINT FK_709123E493CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_media_collection DROP FOREIGN KEY FK_8B7DE97BB52E685C');
        $this->addSql('ALTER TABLE user_media_collection_file DROP FOREIGN KEY FK_709123E49FED072E');
        $this->addSql('DROP TABLE media_collection');
        $this->addSql('DROP TABLE user_media_collection');
        $this->addSql('DROP TABLE user_media_collection_file');
    }
}
