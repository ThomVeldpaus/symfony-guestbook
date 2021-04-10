<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210410072356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Add the sting slug to the table
        $this->addSql('ALTER TABLE conference ADD slug VARCHAR(255)');
        // update the values of the slug field to a concatination of city and year
        $this->addSql("UPDATE conference SET slug=CONCAT(LOWER(city), '-', year)");
        // after all fields contain data, set the slug field to not nullable
        $this->addSql('ALTER TABLE conference ALTER COLUMN slug SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE conference DROP slug');
    }
}
