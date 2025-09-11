<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911123729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "comment" (id UUID NOT NULL, author_id UUID NOT NULL, game_entry_id UUID NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_moderated BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON "comment" (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C1172B664 ON "comment" (game_entry_id)');
        $this->addSql('COMMENT ON COLUMN "comment".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "comment".author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "comment".game_entry_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "comment" ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "comment" ADD CONSTRAINT FK_9474526C1172B664 FOREIGN KEY (game_entry_id) REFERENCES "game_entry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "comment" DROP CONSTRAINT FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE "comment" DROP CONSTRAINT FK_9474526C1172B664');
        $this->addSql('DROP TABLE "comment"');
    }
}
