<?php

declare(strict_types=1);

namespace App\Auth\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210123105046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account_profiles (user_id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, birthday TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, PRIMARY KEY(user_id))');
        $this->addSql('COMMENT ON COLUMN account_profiles.user_id IS \'(DC2Type:account_id)\'');
        $this->addSql('COMMENT ON COLUMN account_profiles.birthday IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN account_profiles.phone IS \'(DC2Type:account_phone)\'');
        $this->addSql('CREATE TABLE accounts (id UUID NOT NULL, email VARCHAR(50) NOT NULL, phone VARCHAR(255) DEFAULT NULL, hash VARCHAR(255) DEFAULT NULL, status VARCHAR(16) NOT NULL, role VARCHAR(16) NOT NULL, confirmed TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, replace_email VARCHAR(255) DEFAULT NULL, blocked TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, registered TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, confirmation_value VARCHAR(255) DEFAULT NULL, confirmation_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_replace_email_value VARCHAR(255) DEFAULT NULL, confirmation_replace_email_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_reset_hash_value VARCHAR(255) DEFAULT NULL, confirmation_reset_hash_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EACE7927C74 ON accounts (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CAC89EAC444F97DD ON accounts (phone)');
        $this->addSql('COMMENT ON COLUMN accounts.id IS \'(DC2Type:account_id)\'');
        $this->addSql('COMMENT ON COLUMN accounts.email IS \'(DC2Type:account_email)\'');
        $this->addSql('COMMENT ON COLUMN accounts.phone IS \'(DC2Type:account_phone)\'');
        $this->addSql('COMMENT ON COLUMN accounts.status IS \'(DC2Type:account_status)\'');
        $this->addSql('COMMENT ON COLUMN accounts.role IS \'(DC2Type:account_role)\'');
        $this->addSql('COMMENT ON COLUMN accounts.confirmed IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.replace_email IS \'(DC2Type:account_email)\'');
        $this->addSql('COMMENT ON COLUMN accounts.blocked IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.registered IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.confirmation_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.confirmation_replace_email_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accounts.confirmation_reset_hash_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE account_profiles ADD CONSTRAINT FK_F3FE6786A76ED395 FOREIGN KEY (user_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account_profiles DROP CONSTRAINT FK_F3FE6786A76ED395');
        $this->addSql('DROP TABLE account_profiles');
        $this->addSql('DROP TABLE accounts');
    }
}
