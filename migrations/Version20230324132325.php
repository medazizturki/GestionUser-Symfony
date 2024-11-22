<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324132325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD nbrparticipants INT NOT NULL, CHANGE image_evenement image_evenement VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE participation CHANGE description_participation description_participation VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP nbrparticipants, CHANGE image_evenement image_evenement VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE participation CHANGE description_participation description_participation VARCHAR(255) DEFAULT NULL');
    }
}
