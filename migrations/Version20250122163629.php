<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122163629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE club (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, presupuesto DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entrenador (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, nombre VARCHAR(100) NOT NULL, salario DOUBLE PRECISION NOT NULL, INDEX IDX_FD19603B61190A32 (club_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jugador (id INT AUTO_INCREMENT NOT NULL, club_id INT DEFAULT NULL, nombre VARCHAR(100) NOT NULL, salario DOUBLE PRECISION NOT NULL, INDEX IDX_527D6F1861190A32 (club_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entrenador ADD CONSTRAINT FK_FD19603B61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE jugador ADD CONSTRAINT FK_527D6F1861190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entrenador DROP FOREIGN KEY FK_FD19603B61190A32');
        $this->addSql('ALTER TABLE jugador DROP FOREIGN KEY FK_527D6F1861190A32');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE entrenador');
        $this->addSql('DROP TABLE jugador');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
