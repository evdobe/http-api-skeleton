<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220216175806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ALTER "timestamp" TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER "timestamp" DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER dispatched_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER dispatched_at DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER received_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER received_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN event.timestamp IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.dispatched_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.received_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE my_aggregate ADD version INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE my_aggregate DROP version');
        $this->addSql('ALTER TABLE event ALTER timestamp TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER timestamp DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER dispatched_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER dispatched_at DROP DEFAULT');
        $this->addSql('ALTER TABLE event ALTER received_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE event ALTER received_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN event."timestamp" IS NULL');
        $this->addSql('COMMENT ON COLUMN event.dispatched_at IS NULL');
        $this->addSql('COMMENT ON COLUMN event.received_at IS NULL');
    }
}
