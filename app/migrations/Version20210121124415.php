<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210121124415 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_users (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(16) NOT NULL, confirm_token VARCHAR(255) DEFAULT NULL, name_first VARCHAR(255) NOT NULL, name_last VARCHAR(255) NOT NULL, reset_token_token VARCHAR(255) DEFAULT NULL, reset_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1E7927C74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1A8C9AA51 ON user_users (confirm_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB186EC69F0 ON user_users (reset_token_token)');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'(DC2Type:user_email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'(DC2Type:user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_users');
    }
}
