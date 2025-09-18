<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250910094323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "game_entry" (id UUID NOT NULL, jam_id UUID NOT NULL, author_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, team_name VARCHAR(255) DEFAULT NULL, media_urls JSON DEFAULT NULL, play_url VARCHAR(500) NOT NULL, tags JSON DEFAULT NULL, is_public BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1912E4FFECB3E09E ON "game_entry" (jam_id)');
        $this->addSql('CREATE INDEX IDX_1912E4FFF675F31B ON "game_entry" (author_id)');
        $this->addSql('COMMENT ON COLUMN "game_entry".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "game_entry".jam_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "game_entry".author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "game_entry" ADD CONSTRAINT FK_1912E4FFECB3E09E FOREIGN KEY (jam_id) REFERENCES "jam" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "game_entry" ADD CONSTRAINT FK_1912E4FFF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "game_entry" DROP CONSTRAINT FK_1912E4FFECB3E09E');
        $this->addSql('ALTER TABLE "game_entry" DROP CONSTRAINT FK_1912E4FFF675F31B');
        $this->addSql('DROP TABLE "game_entry"');
    }
}
