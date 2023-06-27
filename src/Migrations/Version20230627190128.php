<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230627190128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'initial';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE areas (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE "BINARY")');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE tablet_updates (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tablet_id INTEGER DEFAULT NULL, update_id INTEGER DEFAULT NULL, status VARCHAR(255) NOT NULL COLLATE "BINARY" --(DC2Type:StateEnum)
        , CONSTRAINT FK_A119C0E31897676B FOREIGN KEY (tablet_id) REFERENCES tablets (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A119C0E3D596EAB1 FOREIGN KEY (update_id) REFERENCES updates (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A119C0E3D596EAB1 ON tablet_updates (update_id)');
        $this->addSql('CREATE INDEX IDX_A119C0E31897676B ON tablet_updates (tablet_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE tablets (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, area_id INTEGER DEFAULT NULL, code VARCHAR(255) NOT NULL COLLATE "BINARY", CONSTRAINT FK_18A20E90BD0F409C FOREIGN KEY (area_id) REFERENCES areas (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_18A20E90BD0F409C ON tablets (area_id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('CREATE TABLE updates (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, area_id INTEGER DEFAULT NULL, actor VARCHAR(255) NOT NULL COLLATE "BINARY", notes VARCHAR(255) DEFAULT NULL COLLATE "BINARY", created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_45481330BD0F409C FOREIGN KEY (area_id) REFERENCES areas (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_45481330BD0F409C ON updates (area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE areas');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE tablet_updates');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE tablets');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\SqlitePlatform'."
        );

        $this->addSql('DROP TABLE updates');
    }
}
