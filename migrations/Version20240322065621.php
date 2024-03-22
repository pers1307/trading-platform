<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322065621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE risk_profile (id INT AUTO_INCREMENT NOT NULL, balance DOUBLE PRECISION NOT NULL, type ENUM(\'deposite\', \'trade\'), persent DOUBLE PRECISION NOT NULL, accaunt_id INT NOT NULL, strategy_id INT NOT NULL, INDEX IDX_CB48C440CB5424E7 (accaunt_id), INDEX IDX_CB48C440D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4'
        );
        $this->addSql('ALTER TABLE risk_profile ADD CONSTRAINT FK_CB48C440CB5424E7 FOREIGN KEY (accaunt_id) REFERENCES accaunt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE risk_profile ADD CONSTRAINT FK_CB48C440D5CAD932 FOREIGN KEY (strategy_id) REFERENCES strategy (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE risk_profile DROP FOREIGN KEY FK_CB48C440CB5424E7');
        $this->addSql('ALTER TABLE risk_profile DROP FOREIGN KEY FK_CB48C440D5CAD932');
        $this->addSql('DROP TABLE risk_profile');
    }
}
