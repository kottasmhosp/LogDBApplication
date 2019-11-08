<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107114643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE public.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE access_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.actions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE exception_logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE hdfs_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logger_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public."user" (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE access_log (id INT NOT NULL, logger_id_id INT NOT NULL, method VARCHAR(255) NOT NULL, requested_resource TEXT NOT NULL, response_status INT NOT NULL, response_size INT NOT NULL, referer VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF7F351039DACAC9 ON access_log (logger_id_id)');
        $this->addSql('CREATE TABLE public.actions (id INT NOT NULL, user_id_id INT NOT NULL, action VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E6C3F539D86650F ON public.actions (user_id_id)');
        $this->addSql('CREATE TABLE exception_logs (id INT NOT NULL, log TEXT NOT NULL, insert_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE hdfs_log (id INT NOT NULL, logger_id_id INT NOT NULL, block_id VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9A1AD4EA39DACAC9 ON hdfs_log (logger_id_id)');
        $this->addSql('CREATE TABLE logger (id INT NOT NULL, source_ip VARCHAR(255) NOT NULL, dest_ip VARCHAR(255) NOT NULL, time_stamp INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE access_log ADD CONSTRAINT FK_EF7F351039DACAC9 FOREIGN KEY (logger_id_id) REFERENCES logger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.actions ADD CONSTRAINT FK_3E6C3F539D86650F FOREIGN KEY (user_id_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hdfs_log ADD CONSTRAINT FK_9A1AD4EA39DACAC9 FOREIGN KEY (logger_id_id) REFERENCES logger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE public.actions DROP CONSTRAINT FK_3E6C3F539D86650F');
        $this->addSql('ALTER TABLE access_log DROP CONSTRAINT FK_EF7F351039DACAC9');
        $this->addSql('ALTER TABLE hdfs_log DROP CONSTRAINT FK_9A1AD4EA39DACAC9');
        $this->addSql('DROP SEQUENCE public.user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE access_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.actions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE exception_logs_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE hdfs_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE logger_id_seq CASCADE');
        $this->addSql('DROP TABLE public."user"');
        $this->addSql('DROP TABLE access_log');
        $this->addSql('DROP TABLE public.actions');
        $this->addSql('DROP TABLE exception_logs');
        $this->addSql('DROP TABLE hdfs_log');
        $this->addSql('DROP TABLE logger');
    }
}
