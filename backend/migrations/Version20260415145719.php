<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260415145719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create api_tokens table';
    }

    public function up(Schema $schema): void
    {
        // Create api_tokens table
        $this->addSql('CREATE TABLE api_tokens (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, token_id VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', revoked_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_valid TINYINT(1) NOT NULL, INDEX IDX_API_TOKENS_TOKEN_ID (token_id), INDEX IDX_API_TOKENS_USER_ID (user_id), UNIQUE INDEX UNIQ_API_TOKENS_TOKEN_ID (token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_tokens ADD CONSTRAINT FK_API_TOKENS_USER_ID FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop api_tokens table
        $this->addSql('DROP TABLE api_tokens');
    }
}
