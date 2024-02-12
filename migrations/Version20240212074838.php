<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212074838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accaunt_history (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, accaunt_id INT NOT NULL, INDEX IDX_7A0B71DCB5424E7 (accaunt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE accaunt_history ADD CONSTRAINT FK_7A0B71DCB5424E7 FOREIGN KEY (accaunt_id) REFERENCES accaunt (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accaunt_history DROP FOREIGN KEY FK_7A0B71DCB5424E7');
        $this->addSql('DROP TABLE accaunt_history');
    }
}
