<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214120508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу accaunt_inflation для хранения расчетов с поправкой на инфляцию';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE accaunt_inflation (id BIGINT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, movement_amount DOUBLE PRECISION DEFAULT 0 NOT NULL, central_bank_key_rate DOUBLE PRECISION NOT NULL, accaunt_inflation_balance DOUBLE PRECISION NOT NULL, deposit_rate DOUBLE PRECISION NOT NULL, accaunt_deposit_balance DOUBLE PRECISION NOT NULL, accaunt_balance DOUBLE PRECISION NOT NULL, accaunt_id INT NOT NULL, INDEX IDX_E27F0853CB5424E7 (accaunt_id), INDEX idx_accaunt_inflation_date (date), UNIQUE INDEX uniq_accaunt_inflation_accaunt_date (accaunt_id, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE accaunt_inflation ADD CONSTRAINT FK_E27F0853CB5424E7 FOREIGN KEY (accaunt_id) REFERENCES accaunt (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE accaunt_inflation DROP FOREIGN KEY FK_E27F0853CB5424E7');
        $this->addSql('DROP TABLE accaunt_inflation');
    }
}
