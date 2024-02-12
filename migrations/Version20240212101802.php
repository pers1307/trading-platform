<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212101802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE trade (id INT AUTO_INCREMENT NOT NULL, type ENUM(\'long\', \'short\'), open_date_time DATETIME NOT NULL, open_price DOUBLE PRECISION NOT NULL, close_date_time DATETIME DEFAULT NULL, close_price DOUBLE PRECISION NOT NULL, stop_loss DOUBLE PRECISION NOT NULL, take_profit DOUBLE PRECISION NOT NULL, lots INT NOT NULL, status ENUM(\'open\', \'close\'), description TEXT NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, stock_id INT NOT NULL, accaunt_id INT NOT NULL, strategy_id INT NOT NULL, INDEX IDX_7E1A4366DCD6110 (stock_id), INDEX IDX_7E1A4366CB5424E7 (accaunt_id), INDEX IDX_7E1A4366D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4'
        );
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366CB5424E7 FOREIGN KEY (accaunt_id) REFERENCES accaunt (id) ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366D5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategy (id) ON DELETE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366DCD6110');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366CB5424E7');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366D5CAD932');
        $this->addSql('DROP TABLE trade');
    }
}
