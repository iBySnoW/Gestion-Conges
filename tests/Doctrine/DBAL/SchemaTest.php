<?php


namespace Doctrine\DBAL;


use DBUtils\FileDB;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public const DB_FILE_PATH = __DIR__ . '/../../testdb.sqlite';

    private Connection $connection;

    private Schema $schema;

    /**
     * @before
     */
    public function init()
    {
        FileDB::initializeDB(self::DB_FILE_PATH);

        $connectionParams = [
            'url' => 'sqlite:///'.self::DB_FILE_PATH
        ];
        $this->connection = DriverManager::getConnection($connectionParams);
        $schemaManager = $this->connection->createSchemaManager();
        $this->schema = $schemaManager->createSchema();
    }

    /**
     * @test
     */
    public function should_generate_SQL_to_create_table()
    {
        $table = $this->schema->createTable("user");
        $table->addColumn('username', 'string');

        $requests = $this->schema->toSql(new SqlitePlatform());

        self::assertThat($requests, self::countOf(1));
        self::assertThat($requests[0], self::equalTo('CREATE TABLE user (username VARCHAR(255) NOT NULL)'));

    }

    /**
     * @test
     */
    public function should_do_schema_migration()
    {
        $table = $this->schema->createTable("user");
        $table->addColumn('id', 'string');
        $table->addColumn('firstname', 'string');
        $table->addColumn('lastname', 'string');
        $requests = $this->schema->toSql(new SqlitePlatform());

        $this->connection->executeQuery($requests[0]);

        $table = $this->connection->executeQuery("SELECT name FROM sqlite_master WHERE type ='table' AND name NOT LIKE 'sqlite_%'")->fetchAssociative();

        self::assertThat($table['name'], self::equalTo('user'));
    }
}
