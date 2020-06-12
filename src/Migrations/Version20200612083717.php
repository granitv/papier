<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200612083717 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, total INT DEFAULT NULL, INDEX IDX_FE866410A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture_coll (facture_id INT NOT NULL, coll_id INT NOT NULL, INDEX IDX_20162EF87F2DEE08 (facture_id), INDEX IDX_20162EF86AA2FA5C (coll_id), PRIMARY KEY(facture_id, coll_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE facture_coll ADD CONSTRAINT FK_20162EF87F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE facture_coll ADD CONSTRAINT FK_20162EF86AA2FA5C FOREIGN KEY (coll_id) REFERENCES coll (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_users ADD basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD CONSTRAINT FK_C25028241BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C25028241BE1FB52 ON app_users (basket_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE facture_coll DROP FOREIGN KEY FK_20162EF87F2DEE08');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_coll');
        $this->addSql('ALTER TABLE app_users DROP FOREIGN KEY FK_C25028241BE1FB52');
        $this->addSql('DROP INDEX UNIQ_C25028241BE1FB52 ON app_users');
        $this->addSql('ALTER TABLE app_users DROP basket_id');
    }
}
