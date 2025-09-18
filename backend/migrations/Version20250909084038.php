<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909084038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // Step 1: Add a temporary UUID column
        $this->addSql('ALTER TABLE "user" ADD id_new UUID DEFAULT NULL');
        
        // Step 2: Generate UUIDs for existing records
        $this->addSql('UPDATE "user" SET id_new = gen_random_uuid()');
        
        // Step 3: Drop the old integer ID column and its sequence
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT IF EXISTS user_pkey');
        $this->addSql('ALTER TABLE "user" DROP COLUMN id');
        $this->addSql('DROP SEQUENCE IF EXISTS user_id_seq CASCADE');
        
        // Step 4: Rename the new UUID column to id and make it the primary key
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id_new TO id');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN id SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
        
        // Step 5: Add the Doctrine comment
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE user_id_seq');
        $this->addSql('SELECT setval(\'user_id_seq\', (SELECT MAX(id) FROM "user"))');
        $this->addSql('ALTER TABLE "user" ALTER id SET DEFAULT nextval(\'user_id_seq\')');
        $this->addSql('COMMENT ON COLUMN "user".id IS NULL');
    }
}
