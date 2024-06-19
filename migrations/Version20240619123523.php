<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619123523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest CHANGE name name VARCHAR(255) NOT NULL, CHANGE surname surname VARCHAR(255) NOT NULL, CHANGE date_of_birth date_of_birth DATE NOT NULL, CHANGE passport_number passport_number VARCHAR(255) NOT NULL, CHANGE country country VARCHAR(255) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35A76ED395 ON guest (user_id)');
        $this->addSql('CREATE INDEX IDX_ACB79A35833D8F43 ON guest (registration_id)');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A79A4AA658');
        $this->addSql('DROP INDEX IDX_62A8A7A79A4AA658 ON registration');
        $this->addSql('ALTER TABLE registration DROP guest_id, DROP registration_details, CHANGE check_in_date check_in_date DATE NOT NULL, CHANGE check_out_date check_out_date DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35A76ED395');
        $this->addSql('ALTER TABLE guest DROP FOREIGN KEY FK_ACB79A35833D8F43');
        $this->addSql('DROP INDEX IDX_ACB79A35A76ED395 ON guest');
        $this->addSql('DROP INDEX IDX_ACB79A35833D8F43 ON guest');
        $this->addSql('ALTER TABLE guest CHANGE user_id user_id INT NOT NULL, CHANGE name name VARCHAR(100) NOT NULL, CHANGE surname surname VARCHAR(100) NOT NULL, CHANGE date_of_birth date_of_birth DATETIME NOT NULL, CHANGE passport_number passport_number VARCHAR(20) NOT NULL, CHANGE country country VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE registration ADD guest_id INT NOT NULL, ADD registration_details LONGTEXT NOT NULL, CHANGE check_in_date check_in_date DATETIME NOT NULL, CHANGE check_out_date check_out_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A79A4AA658 FOREIGN KEY (guest_id) REFERENCES guest (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_62A8A7A79A4AA658 ON registration (guest_id)');
    }
}
