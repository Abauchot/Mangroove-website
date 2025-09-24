<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924093544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE vote_id_seq CASCADE');
        $this->addSql('CREATE TABLE theme_proposals (id UUID NOT NULL, jam_id UUID NOT NULL, author_id UUID NOT NULL, text VARCHAR(100) NOT NULL, score INT DEFAULT 0 NOT NULL, phase VARCHAR(20) DEFAULT \'submission\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B79E002ECB3E09E ON theme_proposals (jam_id)');
        $this->addSql('CREATE INDEX IDX_6B79E002F675F31B ON theme_proposals (author_id)');
        $this->addSql('COMMENT ON COLUMN theme_proposals.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN theme_proposals.jam_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN theme_proposals.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN theme_proposals.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "theme_vote" (id UUID NOT NULL, theme_proposal_id UUID NOT NULL, voter_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_10D771591C523357 ON "theme_vote" (theme_proposal_id)');
        $this->addSql('CREATE INDEX IDX_10D77159EBB4B8AD ON "theme_vote" (voter_id)');
        $this->addSql('COMMENT ON COLUMN "theme_vote".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "theme_vote".theme_proposal_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "theme_vote".voter_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "theme_vote".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE theme_proposals ADD CONSTRAINT FK_6B79E002ECB3E09E FOREIGN KEY (jam_id) REFERENCES "jam" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE theme_proposals ADD CONSTRAINT FK_6B79E002F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "theme_vote" ADD CONSTRAINT FK_10D771591C523357 FOREIGN KEY (theme_proposal_id) REFERENCES theme_proposals (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "theme_vote" ADD CONSTRAINT FK_10D77159EBB4B8AD FOREIGN KEY (voter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE vote ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE vote ALTER game_entry_id SET NOT NULL');
        $this->addSql('ALTER TABLE vote ALTER voter_id SET NOT NULL');
        $this->addSql('ALTER TABLE vote RENAME COLUMN creeated_at TO created_at');
        $this->addSql('COMMENT ON COLUMN vote.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE vote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE theme_proposals DROP CONSTRAINT FK_6B79E002ECB3E09E');
        $this->addSql('ALTER TABLE theme_proposals DROP CONSTRAINT FK_6B79E002F675F31B');
        $this->addSql('ALTER TABLE "theme_vote" DROP CONSTRAINT FK_10D771591C523357');
        $this->addSql('ALTER TABLE "theme_vote" DROP CONSTRAINT FK_10D77159EBB4B8AD');
        $this->addSql('DROP TABLE theme_proposals');
        $this->addSql('DROP TABLE "theme_vote"');
        $this->addSql('ALTER TABLE "vote" ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE vote_id_seq');
        $this->addSql('SELECT setval(\'vote_id_seq\', (SELECT MAX(id) FROM "vote"))');
        $this->addSql('ALTER TABLE "vote" ALTER id SET DEFAULT nextval(\'vote_id_seq\')');
        $this->addSql('ALTER TABLE "vote" ALTER game_entry_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "vote" ALTER voter_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "vote" RENAME COLUMN created_at TO creeated_at');
        $this->addSql('COMMENT ON COLUMN "vote".id IS NULL');
    }
}
