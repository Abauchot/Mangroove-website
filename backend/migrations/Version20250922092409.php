<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250922092409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE vote_id_seq CASCADE');
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
