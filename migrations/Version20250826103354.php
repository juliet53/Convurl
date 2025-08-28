<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826103354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F47645AE2F727085 ON url (original)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F47645AE78B5DC1 ON url (shortened)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F47645AE2F727085 ON url');
        $this->addSql('DROP INDEX UNIQ_F47645AE78B5DC1 ON url');
    }
}
