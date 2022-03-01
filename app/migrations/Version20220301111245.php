<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220301111245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', rows INT NOT NULL, columns INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', board_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', access_token CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_232B318CE7EC5785 (board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', game_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', snapshot JSON NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_27BA704BE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE piece (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', player_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', x INT NOT NULL, y INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_44CA0B2399E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', game_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, sequence INT DEFAULT 0 NOT NULL, state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_98197A65E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B2399E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE7EC5785');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BE48FD905');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('ALTER TABLE piece DROP FOREIGN KEY FK_44CA0B2399E6F5DF');
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE piece');
        $this->addSql('DROP TABLE player');
    }
}
