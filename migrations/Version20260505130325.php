<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505130325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac DROP CONSTRAINT fk_1ab651f67b3b43d');
        $this->addSql('DROP INDEX idx_1ab651f67b3b43d');
        $this->addSql('ALTER TABLE sac RENAME COLUMN users_id TO user_id');
        $this->addSql('ALTER TABLE sac ADD CONSTRAINT FK_1AB651FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id)');
        $this->addSql('CREATE INDEX IDX_1AB651FA76ED395 ON sac (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac DROP CONSTRAINT FK_1AB651FA76ED395');
        $this->addSql('DROP INDEX IDX_1AB651FA76ED395');
        $this->addSql('ALTER TABLE sac RENAME COLUMN user_id TO users_id');
        $this->addSql('ALTER TABLE sac ADD CONSTRAINT fk_1ab651f67b3b43d FOREIGN KEY (users_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1ab651f67b3b43d ON sac (users_id)');
    }
}
