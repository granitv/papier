<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200707212154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_coll (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_coll_coll (category_coll_id INT NOT NULL, coll_id INT NOT NULL, INDEX IDX_237BA56AAD7ABA22 (category_coll_id), INDEX IDX_237BA56A6AA2FA5C (coll_id), PRIMARY KEY(category_coll_id, coll_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_coll_coll ADD CONSTRAINT FK_237BA56AAD7ABA22 FOREIGN KEY (category_coll_id) REFERENCES category_coll (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_coll_coll ADD CONSTRAINT FK_237BA56A6AA2FA5C FOREIGN KEY (coll_id) REFERENCES coll (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE page_data');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category_coll_coll DROP FOREIGN KEY FK_237BA56AAD7ABA22');
        $this->addSql('CREATE TABLE page_data (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE category_coll');
        $this->addSql('DROP TABLE category_coll_coll');
    }
}
