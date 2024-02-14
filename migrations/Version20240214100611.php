<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214100611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock ADD sec_id VARCHAR(255) NOT NULL, ADD price DOUBLE PRECISION NOT NULL, ADD lot_size SMALLINT NOT NULL, ADD min_step DOUBLE PRECISION NOT NULL, ADD updated DATETIME NOT NULL');
        $this->addSql('CREATE INDEX sec_idx ON stock (sec_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX sec_idx ON stock');
        $this->addSql('ALTER TABLE stock DROP sec_id, DROP price, DROP lot_size, DROP min_step, DROP updated');
    }
}
