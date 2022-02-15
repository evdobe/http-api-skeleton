<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220215230418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id SERIAL NOT NULL, channel VARCHAR(255) NOT NULL, correlation_id INT DEFAULT NULL, aggregate_id INT NOT NULL, aggregate_version INT NOT NULL, data JSON NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, dispatched BOOLEAN DEFAULT false NOT NULL, dispatched_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, received_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, projected BOOLEAN DEFAULT false NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE my_aggregate (id INT NOT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE my_aggregate');
    }
}
