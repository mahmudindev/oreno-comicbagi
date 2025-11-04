<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251109131649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE comic_chapter_provider (
              id BIGINT AUTO_INCREMENT NOT NULL,
              chapter_id BIGINT NOT NULL,
              link_id BIGINT NOT NULL,
              language_id BIGINT,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              ulid BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)',
              released_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              INDEX IDX_C9983450579F4768 (chapter_id),
              INDEX IDX_C9983450ADA40271 (link_id),
              INDEX IDX_C998345082F1BAF4 (language_id),
              UNIQUE INDEX UNIQ_C9983450579F4768C288C859 (chapter_id, ulid),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comic_provider (
              id BIGINT AUTO_INCREMENT NOT NULL,
              comic_id BIGINT NOT NULL,
              link_id BIGINT NOT NULL,
              language_id BIGINT,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              ulid BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)',
              released_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              INDEX IDX_F53D8424D663094A (comic_id),
              INDEX IDX_F53D8424ADA40271 (link_id),
              INDEX IDX_F53D842482F1BAF4 (language_id),
              UNIQUE INDEX UNIQ_F53D8424D663094AC288C859 (comic_id, ulid),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_chapter_provider
            ADD
              CONSTRAINT FK_C9983450579F4768 FOREIGN KEY (chapter_id) REFERENCES comic_chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_chapter_provider
            ADD
              CONSTRAINT FK_C9983450ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_chapter_provider
            ADD
              CONSTRAINT FK_C998345082F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_provider
            ADD
              CONSTRAINT FK_F53D8424D663094A FOREIGN KEY (comic_id) REFERENCES comic (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_provider
            ADD
              CONSTRAINT FK_F53D8424ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_provider
            ADD
              CONSTRAINT FK_F53D842482F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE
        SQL);
        $this->addSql('ALTER TABLE comic_chapter_destination_link DROP FOREIGN KEY FK_D273F19ADA40271');
        $this->addSql('ALTER TABLE comic_chapter_destination_link DROP FOREIGN KEY FK_D273F19579F4768');
        $this->addSql('ALTER TABLE comic_destination_link DROP FOREIGN KEY FK_28483A09ADA40271');
        $this->addSql('ALTER TABLE comic_destination_link DROP FOREIGN KEY FK_28483A09D663094A');
        $this->addSql('ALTER TABLE website_item_language DROP FOREIGN KEY FK_96F69B4182F1BAF4');
        $this->addSql('ALTER TABLE website_item_language DROP FOREIGN KEY FK_96F69B4118F45C82');
        $this->addSql('ALTER TABLE link_item_language DROP FOREIGN KEY FK_CD4EB56282F1BAF4');
        $this->addSql('ALTER TABLE link_item_language DROP FOREIGN KEY FK_CD4EB562ADA40271');
        $this->addSql('DROP TABLE comic_chapter_destination_link');
        $this->addSql('DROP TABLE comic_destination_link');
        $this->addSql('DROP TABLE website_item_language');
        $this->addSql('DROP TABLE link_item_language');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              link
            CHANGE
              relative_reference relative_reference VARCHAR(255) DEFAULT '/' NOT NULL COLLATE `utf8mb4_bin`
        SQL);
        $this->addSql('ALTER TABLE website CHANGE host host VARCHAR(64) NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE website ADD redacted TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE comic_chapter_destination_link (
              id BIGINT AUTO_INCREMENT NOT NULL,
              chapter_id BIGINT NOT NULL,
              link_id BIGINT NOT NULL,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              ulid BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)',
              released_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              UNIQUE INDEX UNIQ_D273F19579F4768C288C859 (chapter_id, ulid),
              INDEX IDX_D273F19579F4768 (chapter_id),
              INDEX IDX_D273F19ADA40271 (link_id),
              UNIQUE INDEX UNIQ_D273F19579F4768ADA40271 (chapter_id, link_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comic_destination_link (
              id BIGINT AUTO_INCREMENT NOT NULL,
              comic_id BIGINT NOT NULL,
              link_id BIGINT NOT NULL,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              ulid BINARY(16) NOT NULL COMMENT '(DC2Type:ulid)',
              released_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              INDEX IDX_28483A09ADA40271 (link_id),
              UNIQUE INDEX UNIQ_28483A09D663094AADA40271 (comic_id, link_id),
              UNIQUE INDEX UNIQ_28483A09D663094AC288C859 (comic_id, ulid),
              INDEX IDX_28483A09D663094A (comic_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE website_item_language (
              id BIGINT AUTO_INCREMENT NOT NULL,
              website_id BIGINT NOT NULL,
              language_id BIGINT NOT NULL,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              machine_translate SMALLINT DEFAULT NULL,
              INDEX IDX_96F69B4118F45C82 (website_id),
              INDEX IDX_96F69B4182F1BAF4 (language_id),
              UNIQUE INDEX UNIQ_96F69B4118F45C8282F1BAF4 (website_id, language_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE link_item_language (
              id BIGINT AUTO_INCREMENT NOT NULL,
              link_id BIGINT NOT NULL,
              language_id BIGINT NOT NULL,
              created_at DATETIME NOT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetimetz_immutable)',
              machine_translate SMALLINT DEFAULT NULL,
              INDEX IDX_CD4EB56282F1BAF4 (language_id),
              UNIQUE INDEX UNIQ_CD4EB562ADA4027182F1BAF4 (link_id, language_id),
              INDEX IDX_CD4EB562ADA40271 (link_id),
              PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = ''
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_chapter_destination_link
            ADD
              CONSTRAINT FK_D273F19ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_chapter_destination_link
            ADD
              CONSTRAINT FK_D273F19579F4768 FOREIGN KEY (chapter_id) REFERENCES comic_chapter (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_destination_link
            ADD
              CONSTRAINT FK_28483A09ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              comic_destination_link
            ADD
              CONSTRAINT FK_28483A09D663094A FOREIGN KEY (comic_id) REFERENCES comic (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              website_item_language
            ADD
              CONSTRAINT FK_96F69B4182F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              website_item_language
            ADD
              CONSTRAINT FK_96F69B4118F45C82 FOREIGN KEY (website_id) REFERENCES website (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              link_item_language
            ADD
              CONSTRAINT FK_CD4EB56282F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              link_item_language
            ADD
              CONSTRAINT FK_CD4EB562ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE
        SQL);
        $this->addSql('ALTER TABLE comic_chapter_provider DROP FOREIGN KEY FK_C9983450579F4768');
        $this->addSql('ALTER TABLE comic_chapter_provider DROP FOREIGN KEY FK_C9983450ADA40271');
        $this->addSql('ALTER TABLE comic_chapter_provider DROP FOREIGN KEY FK_C998345082F1BAF4');
        $this->addSql('ALTER TABLE comic_provider DROP FOREIGN KEY FK_F53D8424D663094A');
        $this->addSql('ALTER TABLE comic_provider DROP FOREIGN KEY FK_F53D8424ADA40271');
        $this->addSql('ALTER TABLE comic_provider DROP FOREIGN KEY FK_F53D842482F1BAF4');
        $this->addSql('DROP TABLE comic_chapter_provider');
        $this->addSql('DROP TABLE comic_provider');
        $this->addSql('ALTER TABLE website CHANGE host host VARCHAR(64) NOT NULL');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              link
            CHANGE
              relative_reference relative_reference VARCHAR(255) DEFAULT '' NOT NULL COLLATE `utf8mb4_bin`
        SQL);
        $this->addSql('ALTER TABLE website DROP redacted');
    }
}
