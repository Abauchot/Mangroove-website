<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922081615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vote (id SERIAL NOT NULL, game_entry_id UUID DEFAULT NULL, voter_id UUID DEFAULT NULL, score INT NOT NULL, creeated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A1085641172B664 ON vote (game_entry_id)');
        $this->addSql('CREATE INDEX IDX_5A108564EBB4B8AD ON vote (voter_id)');
        $this->addSql('COMMENT ON COLUMN vote.game_entry_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vote.voter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vote.creeated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085641172B664 FOREIGN KEY (game_entry_id) REFERENCES "game_entry" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A1085641172B664');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A108564EBB4B8AD');
        $this->addSql('DROP TABLE vote');
    }
}
