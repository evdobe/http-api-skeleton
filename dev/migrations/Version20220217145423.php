<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220217145423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE my_aggregate ADD status_by VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE my_aggregate ADD status_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE my_aggregate RENAME COLUMN status TO status_code');
        $this->addSql('COMMENT ON COLUMN my_aggregate.status_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE my_aggregate ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE my_aggregate DROP status_code');
        $this->addSql('ALTER TABLE my_aggregate DROP status_by');
        $this->addSql('ALTER TABLE my_aggregate DROP status_at');
    }
}
