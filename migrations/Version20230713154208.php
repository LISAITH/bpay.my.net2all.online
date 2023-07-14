<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713154208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recharges DROP FOREIGN KEY FK_7D4DE4619651EEC');
        $this->addSql('DROP TABLE recharges');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recharges (id INT AUTO_INCREMENT NOT NULL, compte_bpay_id INT DEFAULT NULL, point_vente_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, date_recharge DATETIME NOT NULL, INDEX IDX_7D4DE46EFA24D68 (point_vente_id), INDEX IDX_7D4DE46DF8F311D (compte_bpay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE recharges ADD CONSTRAINT FK_7D4DE4619651EEC FOREIGN KEY (compte_bpay_id) REFERENCES compte_bpay (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
