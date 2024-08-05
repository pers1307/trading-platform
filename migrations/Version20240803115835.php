<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240803115835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deal (id INT AUTO_INCREMENT NOT NULL, sec_id VARCHAR(255) DEFAULT NULL, type ENUM(\'long\', \'short\'), price DOUBLE PRECISION NOT NULL, lots INT NOT NULL, date_time DATETIME NOT NULL, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, accaunt_id INT NOT NULL, stock_id INT DEFAULT NULL, INDEX IDX_E3FEC116CB5424E7 (accaunt_id), INDEX IDX_E3FEC116DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116CB5424E7 FOREIGN KEY (accaunt_id) REFERENCES accaunt (id) ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id) ON DELETE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC116CB5424E7');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC116DCD6110');
        $this->addSql('DROP TABLE deal');
    }
}
