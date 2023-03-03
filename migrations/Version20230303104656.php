<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230303104656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CA7294869C');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CA7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE user ADD image_name VARCHAR(255) NOT NULL, ADD image_ext VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP image_name, DROP image_ext');
        $this->addSql('ALTER TABLE commentary DROP FOREIGN KEY FK_1CAC12CA7294869C');
        $this->addSql('ALTER TABLE commentary ADD CONSTRAINT FK_1CAC12CA7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
