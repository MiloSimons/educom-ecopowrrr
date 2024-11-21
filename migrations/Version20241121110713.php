<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121110713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, client_advisor_id INT DEFAULT NULL, bank_account_info VARCHAR(255) NOT NULL, zip_code VARCHAR(20) NOT NULL, house_number VARCHAR(10) NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, municipality VARCHAR(255) NOT NULL, province VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, age INT NOT NULL, gender VARCHAR(50) NOT NULL, type VARCHAR(1) NOT NULL, INDEX IDX_C7440455F6879036 (client_advisor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, overall_device_id INT NOT NULL, serial_number VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_92FB68E1486EB09 (overall_device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_metrics (id INT AUTO_INCREMENT NOT NULL, device_id INT NOT NULL, status_id INT NOT NULL, price_id INT NOT NULL, total_yield DOUBLE PRECISION NOT NULL, monthly_yield DOUBLE PRECISION NOT NULL, date DATE NOT NULL, INDEX IDX_A83C737A94A4C7D4 (device_id), INDEX IDX_A83C737A6BF700BD (status_id), INDEX IDX_A83C737AD614C7E7 (price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE overall_device (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, status_id INT NOT NULL, total_kw_hused DOUBLE PRECISION NOT NULL, monthly_kw_hused DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_691A371219EB6921 (client_id), INDEX IDX_691A37126BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price (id INT AUTO_INCREMENT NOT NULL, buy_in_price DOUBLE PRECISION NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455F6879036 FOREIGN KEY (client_advisor_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E1486EB09 FOREIGN KEY (overall_device_id) REFERENCES overall_device (id)');
        $this->addSql('ALTER TABLE device_metrics ADD CONSTRAINT FK_A83C737A94A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE device_metrics ADD CONSTRAINT FK_A83C737A6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE device_metrics ADD CONSTRAINT FK_A83C737AD614C7E7 FOREIGN KEY (price_id) REFERENCES price (id)');
        $this->addSql('ALTER TABLE overall_device ADD CONSTRAINT FK_691A371219EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE overall_device ADD CONSTRAINT FK_691A37126BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455F6879036');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E1486EB09');
        $this->addSql('ALTER TABLE device_metrics DROP FOREIGN KEY FK_A83C737A94A4C7D4');
        $this->addSql('ALTER TABLE device_metrics DROP FOREIGN KEY FK_A83C737A6BF700BD');
        $this->addSql('ALTER TABLE device_metrics DROP FOREIGN KEY FK_A83C737AD614C7E7');
        $this->addSql('ALTER TABLE overall_device DROP FOREIGN KEY FK_691A371219EB6921');
        $this->addSql('ALTER TABLE overall_device DROP FOREIGN KEY FK_691A37126BF700BD');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_metrics');
        $this->addSql('DROP TABLE overall_device');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
