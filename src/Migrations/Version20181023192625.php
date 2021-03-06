<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181023192625 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart_products DROP FOREIGN KEY FK_2D251531DE18E50B');
        $this->addSql('DROP INDEX IDX_2D251531DE18E50B ON cart_products');
        $this->addSql('ALTER TABLE cart_products CHANGE product_id_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D2515314584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2D2515314584665A ON cart_products (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart_products DROP FOREIGN KEY FK_2D2515314584665A');
        $this->addSql('DROP INDEX IDX_2D2515314584665A ON cart_products');
        $this->addSql('ALTER TABLE cart_products CHANGE product_id product_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D251531DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2D251531DE18E50B ON cart_products (product_id_id)');
    }
}
