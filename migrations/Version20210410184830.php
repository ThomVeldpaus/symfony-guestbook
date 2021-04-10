<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210410184830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // First add state field to the table
        $this->addSql('ALTER TABLE comment ADD state VARCHAR(255)');

        // Update values of all existing state columns to 'published'
        $this->addSql("UPDATE comment SET state='published'");

        // Configure the state field not nullable
        $this->addSql('ALTER TABLE comment ALTER COLUMN state SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP state');
    }
}
