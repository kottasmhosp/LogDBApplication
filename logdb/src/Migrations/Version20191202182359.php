<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191202182359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE public.logger_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.actions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.hdfs_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.access_log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.block_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.exception_logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public.logger (id INT NOT NULL, source_ip VARCHAR(255) NOT NULL, dest_ip VARCHAR(255) NOT NULL, insert_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE public.actions (id INT NOT NULL, user_id_id INT NOT NULL, action TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E6C3F539D86650F ON public.actions (user_id_id)');
        $this->addSql('CREATE TABLE public.hdfs_log (id INT NOT NULL, logger_id_id INT NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_58F60F8339DACAC9 ON public.hdfs_log (logger_id_id)');
        $this->addSql('CREATE TABLE hdfs_log_block (hdfs_log_id INT NOT NULL, block_id INT NOT NULL, PRIMARY KEY(hdfs_log_id, block_id))');
        $this->addSql('CREATE INDEX IDX_DA9101EDA0269BD ON hdfs_log_block (hdfs_log_id)');
        $this->addSql('CREATE INDEX IDX_DA9101EE9ED820C ON hdfs_log_block (block_id)');
        $this->addSql('CREATE TABLE public."user" (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(500) NOT NULL, is_active BOOLEAN NOT NULL, address VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE public.access_log (id INT NOT NULL, logger_id_id INT NOT NULL, method VARCHAR(255) NOT NULL, requested_resource TEXT NOT NULL, response_status INT NOT NULL, response_size INT NOT NULL, referer TEXT NOT NULL, user_agent TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4A412C4E39DACAC9 ON public.access_log (logger_id_id)');
        $this->addSql('CREATE TABLE public.block (id INT NOT NULL, block_number BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE public.exception_logs (id INT NOT NULL, log TEXT NOT NULL, reason VARCHAR(255) DEFAULT NULL, insert_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE public.actions ADD CONSTRAINT FK_3E6C3F539D86650F FOREIGN KEY (user_id_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.hdfs_log ADD CONSTRAINT FK_58F60F8339DACAC9 FOREIGN KEY (logger_id_id) REFERENCES public.logger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hdfs_log_block ADD CONSTRAINT FK_DA9101EDA0269BD FOREIGN KEY (hdfs_log_id) REFERENCES public.hdfs_log (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hdfs_log_block ADD CONSTRAINT FK_DA9101EE9ED820C FOREIGN KEY (block_id) REFERENCES public.block (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.access_log ADD CONSTRAINT FK_4A412C4E39DACAC9 FOREIGN KEY (logger_id_id) REFERENCES public.logger (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE public.hdfs_log DROP CONSTRAINT FK_58F60F8339DACAC9');
        $this->addSql('ALTER TABLE public.access_log DROP CONSTRAINT FK_4A412C4E39DACAC9');
        $this->addSql('ALTER TABLE hdfs_log_block DROP CONSTRAINT FK_DA9101EDA0269BD');
        $this->addSql('ALTER TABLE public.actions DROP CONSTRAINT FK_3E6C3F539D86650F');
        $this->addSql('ALTER TABLE hdfs_log_block DROP CONSTRAINT FK_DA9101EE9ED820C');
        $this->addSql('DROP SEQUENCE public.logger_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.actions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.hdfs_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.access_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.block_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.exception_logs_id_seq CASCADE');
        $this->addSql('DROP TABLE public.logger');
        $this->addSql('DROP TABLE public.actions');
        $this->addSql('DROP TABLE public.hdfs_log');
        $this->addSql('DROP TABLE hdfs_log_block');
        $this->addSql('DROP TABLE public."user"');
        $this->addSql('DROP TABLE public.access_log');
        $this->addSql('DROP TABLE public.block');
        $this->addSql('DROP TABLE public.exception_logs');
    }
}
