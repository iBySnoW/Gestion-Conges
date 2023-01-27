<?php

namespace App\Conge;

use App\Model\Conge;
use App\Repository\CongeRepository;
use DBUtils\FileDB;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Exception;
use PHPUnit\Framework\TestCase;

class CongeRepositoryTest extends TestCase
{
    private $container;

    private Connection $pdoConnection;

    /**
     * @before
     */
    public function init()
    {
        $this->container = include __DIR__ . '/../../app/bootstrap.php';

        FileDB::initializeDB($this->container->get('db.file'));
        $this->pdoConnection = DriverManager::getConnection($this->container->get('connection.params'));
        $this->pdoConnection->executeStatement("CREATE TABLE conge (
            id varchar(255) not null primary key,
            startDate date not null,
            endDate date not null,
            type varchar(255) not null,
            days integer not null,
            employee  varchar(255) default UNKNOWN not null
            
            )");
        $this->pdoConnection->executeStatement("INSERT INTO user (id, startDate, endDate, type, days, employee) VALUES ('626e9b71-54f6-44fd-9539-0120cf37daf7', '2022-06-10', '2002-06-15', 'CP', 4,'626e9b71-54f6-44fd-9539-0120cf37daf6')");
        $this->pdoConnection->executeStatement("INSERT INTO user (id, startDate, endDate, type, days, employee) VALUES ('626e9b71-54f6-44fd-9539-0120cf37daf8', '2022-06-02', '2002-06-03', 'RTT', 2, '626e9b71-54f6-44fd-9539-0120cf37daf6')");
    }
    /**
     * @test
     */
    public function should_list_users_from_user_table()
    {
        $repository = new CongeRepository($this->container->get('Connection'));
        $conges = $repository->list();

        self::assertThat($conges, self::countOf(2));
        self::assertThat($conges[0], self::isInstanceOf(Conge::class));
    }

    /**
     * @test
     */
    public function should_get_user_from_id()
    {
        $repository = new CongeRepository($this->container->get('Connection'));
        $conge = $repository->get('626e9b71-54f6-44fd-9539-0120cf37daf7');

        self::assertThat($conge, self::isInstanceOf(Conge::class));
        self::assertThat($conge->getId(), self::equalTo('626e9b71-54f6-44fd-9539-0120cf37daf7'));
    }

    /**
     * @test
     */
    public function should_add_user_to_user_table()
    {
        $repository = new CongeRepository($this->container->get('Connection'));

        $repository->add(new Conge('626e9b71-54f6-44fd-9539-0120cf37daf9','626e9b71-54f6-44fd-9539-0120cf37daf6', '2022-07-15', '2022-07-29', 'CP', 11));

        $record = $this->pdoConnection
            ->executeQuery("SELECT id, startDate, endDate, type, days FROM user WHERE id ='626e9b71-54f6-44fd-9539-0120cf37daf9'")
            ->fetchAssociative(\PDO::FETCH_ASSOC);
        self::assertThat($record['id'], self::equalTo('626e9b71-54f6-44fd-9539-0120cf37daf9'));
        self::assertThat($record['starDate'], self::equalTo('2022-07-16'));
        self::assertThat($record['endDate'], self::equalTo('2022-07-29'));
        self::assertThat($record['type'], self::equalTo('CP'));
    }

    /**
     * @test
     */
    public function should_delete_user_from_user_table()
    {
        $repository = new CongeRepository($this->container->get('Connection'));
        $affectedRows = $repository->delete('626e9b71-54f6-44fd-9539-0120cf37daf7');
        self::assertThat($affectedRows, self::equalTo(1));
        $records = $this->pdoConnection
            ->executeQuery("SELECT id, startDate, endDate, type, days, employee FROM conge")
            ->fetchAllAssociative();
        self::assertThat($records, self::countOf(1));
    }

    /**
     * @test
     */
    public function should_throw_exception_when_delete_unknown_user()
    {
        $repository = new CongeRepository($this->container->get('Connection'));

        $affectedRows = $repository->delete('4ef3e75e-bdae-4fbe-8584-f21fbb39bbcv');

        self::assertThat($affectedRows, self::equalTo(0));
    }
}
