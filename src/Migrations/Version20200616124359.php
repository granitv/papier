<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616124359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE basket ADD userinfo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BC1EA3D46 FOREIGN KEY (userinfo_id) REFERENCES user_info (id)');
        $this->addSql('CREATE INDEX IDX_2246507BC1EA3D46 ON basket (userinfo_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BC1EA3D46');
        $this->addSql('DROP INDEX IDX_2246507BC1EA3D46 ON basket');
        $this->addSql('ALTER TABLE basket DROP userinfo_id');
    }
}
