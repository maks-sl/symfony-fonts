<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205145528 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE font_faces (id UUID NOT NULL, font_id UUID NOT NULL, name VARCHAR(255) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92C87631D7F7F9EB ON font_faces (font_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92C87631D7F7F9EB5124F222 ON font_faces (font_id, sort)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92C87631D7F7F9EB5E237E06 ON font_faces (font_id, name)');
        $this->addSql('COMMENT ON COLUMN font_faces.id IS \'(DC2Type:font_face_id)\'');
        $this->addSql('COMMENT ON COLUMN font_faces.font_id IS \'(DC2Type:font_id)\'');
        $this->addSql('CREATE TABLE font_files (id UUID NOT NULL, font_id UUID NOT NULL, face_id UUID DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, info_path VARCHAR(255) NOT NULL, info_name VARCHAR(255) NOT NULL, info_ext VARCHAR(255) NOT NULL, info_size INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C2019E3D7F7F9EB ON font_files (font_id)');
        $this->addSql('CREATE INDEX IDX_5C2019E3FDC86CD0 ON font_files (face_id)');
        $this->addSql('CREATE INDEX IDX_5C2019E3AA9E377A ON font_files (date)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C2019E3523F5981748AC88D93C0F7E ON font_files (info_path, info_name, info_ext)');
        $this->addSql('COMMENT ON COLUMN font_files.id IS \'(DC2Type:font_file_id)\'');
        $this->addSql('COMMENT ON COLUMN font_files.font_id IS \'(DC2Type:font_id)\'');
        $this->addSql('COMMENT ON COLUMN font_files.face_id IS \'(DC2Type:font_face_id)\'');
        $this->addSql('COMMENT ON COLUMN font_files.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE font_fonts (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, files_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, author VARCHAR(128) NOT NULL, status VARCHAR(16) NOT NULL, license VARCHAR(16) NOT NULL, languages JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2916B141AA9E377A ON font_fonts (date)');
        $this->addSql('COMMENT ON COLUMN font_fonts.id IS \'(DC2Type:font_id)\'');
        $this->addSql('COMMENT ON COLUMN font_fonts.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN font_fonts.files_updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN font_fonts.status IS \'(DC2Type:font_status)\'');
        $this->addSql('COMMENT ON COLUMN font_fonts.license IS \'(DC2Type:font_license)\'');
        $this->addSql('COMMENT ON COLUMN font_fonts.languages IS \'(DC2Type:font_languages)\'');
        $this->addSql('ALTER TABLE font_faces ADD CONSTRAINT FK_92C87631D7F7F9EB FOREIGN KEY (font_id) REFERENCES font_fonts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE font_files ADD CONSTRAINT FK_5C2019E3D7F7F9EB FOREIGN KEY (font_id) REFERENCES font_fonts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE font_files ADD CONSTRAINT FK_5C2019E3FDC86CD0 FOREIGN KEY (face_id) REFERENCES font_faces (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE font_files DROP CONSTRAINT FK_5C2019E3FDC86CD0');
        $this->addSql('ALTER TABLE font_faces DROP CONSTRAINT FK_92C87631D7F7F9EB');
        $this->addSql('ALTER TABLE font_files DROP CONSTRAINT FK_5C2019E3D7F7F9EB');
        $this->addSql('DROP TABLE font_faces');
        $this->addSql('DROP TABLE font_files');
        $this->addSql('DROP TABLE font_fonts');
    }
}
