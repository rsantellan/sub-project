<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190729194131 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_fee (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, identity VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, payment VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE news CHANGE slug slug VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE type type SMALLINT DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_inscription CHANGE level level SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_role CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_sections CHANGE position position SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE maith_file CHANGE album_id album_id INT DEFAULT NULL, CHANGE onlinevideo onlinevideo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE maith_album CHANGE hasonlinevideo hasonlinevideo TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_fee');
        $this->addSql('ALTER TABLE fos_role CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE maith_album CHANGE hasonlinevideo hasonlinevideo TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE maith_file CHANGE album_id album_id INT DEFAULT NULL, CHANGE onlinevideo onlinevideo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE news CHANGE slug slug VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE type type SMALLINT DEFAULT NULL, CHANGE subtitle subtitle VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE sub_inscription CHANGE level level SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_sections CHANGE position position SMALLINT DEFAULT NULL');
    }
}
