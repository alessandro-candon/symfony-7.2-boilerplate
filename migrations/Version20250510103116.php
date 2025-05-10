<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510103116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, client VARCHAR(32) NOT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_454D9673C7440455 ON oauth2_access_token (client)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_access_token.expiry IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_access_token.scopes IS '(DC2Type:oauth2_scope)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, client VARCHAR(32) NOT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL, revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_509FEF5FC7440455 ON oauth2_authorization_code (client)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_authorization_code.expiry IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_authorization_code.scopes IS '(DC2Type:oauth2_scope)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_client (identifier VARCHAR(32) NOT NULL, name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris TEXT DEFAULT NULL, grants TEXT DEFAULT NULL, scopes TEXT DEFAULT NULL, active BOOLEAN NOT NULL, allow_plain_text_pkce BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(identifier))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_client.redirect_uris IS '(DC2Type:oauth2_redirect_uri)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_client.grants IS '(DC2Type:oauth2_grant)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_client.scopes IS '(DC2Type:oauth2_scope)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, access_token CHAR(80) DEFAULT NULL, expiry TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4DD90732B6A2DD68 ON oauth2_refresh_token (access_token)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN oauth2_refresh_token.expiry IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, first_name VARCHAR(180) NOT NULL, last_name VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, can_receive_notifications BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_authorization_code ADD CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_access_token DROP CONSTRAINT FK_454D9673C7440455
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_authorization_code DROP CONSTRAINT FK_509FEF5FC7440455
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE oauth2_refresh_token DROP CONSTRAINT FK_4DD90732B6A2DD68
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE oauth2_access_token
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE oauth2_authorization_code
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE oauth2_client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE oauth2_refresh_token
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
    }
}
