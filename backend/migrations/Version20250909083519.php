<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909083519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // First, backup existing data if needed and handle ID conversion
        // Drop foreign key constraints if any exist
        
        // Add new UUID column temporarily
        $this->addSql('ALTER TABLE "user" ADD id_new UUID DEFAULT NULL');
        
        // Generate UUIDs for existing records
        $this->addSql('UPDATE "user" SET id_new = gen_random_uuid()');
        
        // Drop the old ID column and sequence
        $this->addSql('ALTER TABLE "user" DROP COLUMN id');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        
        // Rename new column to id and make it primary key
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id_new TO id');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN id SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
        
        // Add other columns
        $this->addSql('ALTER TABLE "user" ADD username VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD display_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD bio TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD avatar_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD category VARCHAR(50) DEFAULT NULL');
        
        // Add created_at and updated_at as nullable first, then set default values
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        
        // Update existing records with current timestamp
        $this->addSql('UPDATE "user" SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL');
        
        // Now make them NOT NULL
        $this->addSql('ALTER TABLE "user" ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN updated_at SET NOT NULL');
        
        // Rename password column
        $this->addSql('ALTER TABLE "user" RENAME COLUMN password TO password_hash');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE "user" DROP username');
        $this->addSql('ALTER TABLE "user" DROP display_name');
        $this->addSql('ALTER TABLE "user" DROP bio');
        $this->addSql('ALTER TABLE "user" DROP avatar_url');
        $this->addSql('ALTER TABLE "user" DROP category');
        $this->addSql('ALTER TABLE "user" DROP created_at');
        $this->addSql('ALTER TABLE "user" DROP updated_at');
        $this->addSql('ALTER TABLE "user" ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE user_id_seq');
        $this->addSql('SELECT setval(\'user_id_seq\', (SELECT MAX(id) FROM "user"))');
        $this->addSql('ALTER TABLE "user" ALTER id SET DEFAULT nextval(\'user_id_seq\')');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN password_hash TO password');
        $this->addSql('COMMENT ON COLUMN "user".id IS NULL');
    }
}
