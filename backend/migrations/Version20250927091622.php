<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927091622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forum_post (id UUID NOT NULL, thread_id UUID NOT NULL, author_id UUID NOT NULL, parent_id UUID DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_996BCC5AE2904019 ON forum_post (thread_id)');
        $this->addSql('CREATE INDEX IDX_996BCC5AF675F31B ON forum_post (author_id)');
        $this->addSql('CREATE INDEX IDX_996BCC5A727ACA70 ON forum_post (parent_id)');
        $this->addSql('COMMENT ON COLUMN forum_post.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_post.thread_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_post.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_post.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE forum_thread (id UUID NOT NULL, author_id UUID NOT NULL, jam_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, is_public BOOLEAN NOT NULL, is_announcement BOOLEAN NOT NULL, pinned BOOLEAN NOT NULL, locked BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_298F7F52F675F31B ON forum_thread (author_id)');
        $this->addSql('CREATE INDEX IDX_298F7F52ECB3E09E ON forum_thread (jam_id)');
        $this->addSql('COMMENT ON COLUMN forum_thread.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_thread.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN forum_thread.jam_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE forum_post ADD CONSTRAINT FK_996BCC5AE2904019 FOREIGN KEY (thread_id) REFERENCES forum_thread (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_post ADD CONSTRAINT FK_996BCC5AF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_post ADD CONSTRAINT FK_996BCC5A727ACA70 FOREIGN KEY (parent_id) REFERENCES forum_post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_thread ADD CONSTRAINT FK_298F7F52F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forum_thread ADD CONSTRAINT FK_298F7F52ECB3E09E FOREIGN KEY (jam_id) REFERENCES "jam" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE forum_post DROP CONSTRAINT FK_996BCC5AE2904019');
        $this->addSql('ALTER TABLE forum_post DROP CONSTRAINT FK_996BCC5AF675F31B');
        $this->addSql('ALTER TABLE forum_post DROP CONSTRAINT FK_996BCC5A727ACA70');
        $this->addSql('ALTER TABLE forum_thread DROP CONSTRAINT FK_298F7F52F675F31B');
        $this->addSql('ALTER TABLE forum_thread DROP CONSTRAINT FK_298F7F52ECB3E09E');
        $this->addSql('DROP TABLE forum_post');
        $this->addSql('DROP TABLE forum_thread');
    }
}
