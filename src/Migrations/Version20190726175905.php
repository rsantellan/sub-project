<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190726175905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_inscription_sections (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_inscription (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identity VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, level SMALLINT DEFAULT NULL, startdate DATE NOT NULL, payment VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_inscription_sub_inscription_sections (sub_inscription_id INT NOT NULL, sub_inscription_sections_id INT NOT NULL, INDEX IDX_683E22223C080B87 (sub_inscription_id), INDEX IDX_683E2222366B5770 (sub_inscription_sections_id), PRIMARY KEY(sub_inscription_id, sub_inscription_sections_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_inscription_sub_inscription_sections ADD CONSTRAINT FK_683E22223C080B87 FOREIGN KEY (sub_inscription_id) REFERENCES sub_inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_inscription_sub_inscription_sections ADD CONSTRAINT FK_683E2222366B5770 FOREIGN KEY (sub_inscription_sections_id) REFERENCES sub_inscription_sections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE news CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE type type SMALLINT DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_role CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_sections CHANGE position position SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE maith_file CHANGE album_id album_id INT DEFAULT NULL, CHANGE onlinevideo onlinevideo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE maith_album CHANGE hasonlinevideo hasonlinevideo TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_inscription_sub_inscription_sections DROP FOREIGN KEY FK_683E2222366B5770');
        $this->addSql('ALTER TABLE sub_inscription_sub_inscription_sections DROP FOREIGN KEY FK_683E22223C080B87');
        $this->addSql('DROP TABLE sub_inscription_sections');
        $this->addSql('DROP TABLE sub_inscription');
        $this->addSql('DROP TABLE sub_inscription_sub_inscription_sections');
        $this->addSql('ALTER TABLE fos_role CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE maith_album CHANGE hasonlinevideo hasonlinevideo TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE maith_file CHANGE album_id album_id INT DEFAULT NULL, CHANGE onlinevideo onlinevideo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE news CHANGE slug slug VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE type type SMALLINT DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE sub_sections CHANGE position position SMALLINT DEFAULT NULL');
    }
}
