<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415170142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add refresh_token_id column to api_tokens table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_tokens ADD refresh_token_id VARCHAR(255) DEFAULT NULL UNIQUE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_tokens DROP INDEX refresh_token_id, DROP COLUMN refresh_token_id');
    }
}
