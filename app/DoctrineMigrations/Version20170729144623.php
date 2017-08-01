<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170729144623 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE slug (id INT AUTO_INCREMENT NOT NULL, text_id INT DEFAULT NULL, body VARCHAR(255) NOT NULL, INDEX IDX_989D9B62698D3548 (text_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE slug ADD CONSTRAINT FK_989D9B62698D3548 FOREIGN KEY (text_id) REFERENCES text (id)');
        $this->addSql('ALTER TABLE text ADD current_slug_id INT DEFAULT NULL, DROP slug');
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
        $this->addSql('DROP TABLE slug');
        $this->addSql('DROP INDEX UNIQ_3B8BA7C79B14E34B ON text');
        $this->addSql('ALTER TABLE text ADD slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP current_slug_id');
    }
}
