<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170729104520 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE text DROP FOREIGN KEY FK_3B8BA7C7327B1071');
        $this->addSql('DROP INDEX UNIQ_3B8BA7C7327B1071 ON text');
        $this->addSql('ALTER TABLE text CHANGE latest_slug_id current_slug_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE text ADD CONSTRAINT FK_3B8BA7C79B14E34B FOREIGN KEY (current_slug_id) REFERENCES slug (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B8BA7C79B14E34B ON text (current_slug_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE text DROP FOREIGN KEY FK_3B8BA7C79B14E34B');
        $this->addSql('DROP INDEX UNIQ_3B8BA7C79B14E34B ON text');
        $this->addSql('ALTER TABLE text CHANGE current_slug_id latest_slug_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE text ADD CONSTRAINT FK_3B8BA7C7327B1071 FOREIGN KEY (latest_slug_id) REFERENCES slug (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B8BA7C7327B1071 ON text (latest_slug_id)');
    }
}
