<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909083826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Add new columns only, keeping the existing ID structure for now
        $this->addSql('ALTER TABLE "user" ADD username VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD display_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD bio TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD avatar_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD category VARCHAR(50) DEFAULT NULL');
        
        // Add timestamp columns as nullable first
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        
        // Update existing records with current timestamp
        $this->addSql('UPDATE "user" SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL');
        
        // Make timestamp columns NOT NULL
        $this->addSql('ALTER TABLE "user" ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN updated_at SET NOT NULL');
        
        // Rename password column
        $this->addSql('ALTER TABLE "user" RENAME COLUMN password TO password_hash');
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
