<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502095251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE trade_close_warning (id INT AUTO_INCREMENT NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, trade_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_99E4EE7BC2D9760 (trade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4'
        );
        $this->addSql('ALTER TABLE trade_close_warning ADD CONSTRAINT FK_99E4EE7BC2D9760 FOREIGN KEY (trade_id) REFERENCES trade (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade_close_warning DROP FOREIGN KEY FK_99E4EE7BC2D9760');
        $this->addSql('DROP TABLE trade_close_warning');
    }
}
