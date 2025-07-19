<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250714184320 extends AbstractMigration
{
	public function getDescription(): string
	{
		return 'Init DB';
	}

	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, author_id INT DEFAULT NULL, INDEX IDX_BFDD3168F675F31B (author_id), PRIMARY KEY (id))');
		$this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY (id))');
		$this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
	}

	public function down(Schema $schema): void
	{
		$this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168F675F31B');
		$this->addSql('DROP TABLE articles');
		$this->addSql('DROP TABLE users');
	}
}
