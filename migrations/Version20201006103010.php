<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201006103010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_adress (id INT AUTO_INCREMENT NOT NULL, adresse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_adress_user (user_adress_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8B36349184667448 (user_adress_id), INDEX IDX_8B363491A76ED395 (user_id), PRIMARY KEY(user_adress_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_adress_user ADD CONSTRAINT FK_8B36349184667448 FOREIGN KEY (user_adress_id) REFERENCES user_adress (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_adress_user ADD CONSTRAINT FK_8B363491A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_adress_user DROP FOREIGN KEY FK_8B36349184667448');
        $this->addSql('DROP TABLE user_adress');
        $this->addSql('DROP TABLE user_adress_user');
    }
}
