<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215082332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade CHANGE type type ENUM(\'long\', \'short\'), CHANGE close_price close_price DOUBLE PRECISION DEFAULT NULL, CHANGE stop_loss stop_loss DOUBLE PRECISION DEFAULT NULL, CHANGE take_profit take_profit DOUBLE PRECISION DEFAULT NULL, CHANGE status status ENUM(\'open\', \'close\'), CHANGE description description TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade CHANGE type type VARCHAR(0) DEFAULT NULL, CHANGE close_price close_price DOUBLE PRECISION NOT NULL, CHANGE stop_loss stop_loss DOUBLE PRECISION NOT NULL, CHANGE take_profit take_profit DOUBLE PRECISION NOT NULL, CHANGE status status VARCHAR(0) DEFAULT NULL, CHANGE description description TEXT NOT NULL');
    }
}
