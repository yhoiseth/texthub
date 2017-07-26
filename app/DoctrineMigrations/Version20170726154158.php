<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170726154158 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE text ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE text ADD CONSTRAINT FK_3B8BA7C7B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_3B8BA7C7B03A8386 ON text (created_by_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE text DROP FOREIGN KEY FK_3B8BA7C7B03A8386');
        $this->addSql('DROP INDEX IDX_3B8BA7C7B03A8386 ON text');
        $this->addSql('ALTER TABLE text DROP created_by_id');
    }
}
