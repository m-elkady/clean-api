<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260414123342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove duplicate emails and add unique constraint';
    }

    public function up(Schema $schema): void
    {
        // Remove duplicate emails (keep the oldest entry based on id)
        $this->addSql('DELETE u1 FROM user u1 INNER JOIN user u2 WHERE u1.id > u2.id AND u1.email = u2.email');

        // Add unique constraint
        $this->addSql('ALTER TABLE `user` ADD UNIQUE INDEX `UNIQ_user_email` (`email`)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP INDEX `UNIQ_user_email`');
    }
}
