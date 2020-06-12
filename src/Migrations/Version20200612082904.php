<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200612082904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE basket_coll (basket_id INT NOT NULL, coll_id INT NOT NULL, INDEX IDX_D9F1B011BE1FB52 (basket_id), INDEX IDX_D9F1B016AA2FA5C (coll_id), PRIMARY KEY(basket_id, coll_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket_coll ADD CONSTRAINT FK_D9F1B011BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket_coll ADD CONSTRAINT FK_D9F1B016AA2FA5C FOREIGN KEY (coll_id) REFERENCES coll (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507BA76ED395 ON basket (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE basket_coll');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA76ED395');
        $this->addSql('DROP INDEX UNIQ_2246507BA76ED395 ON basket');
        $this->addSql('ALTER TABLE basket DROP user_id');
    }
}
