<?php

namespace App\User;

use App\Model\User;
use App\Repository\UserRepository;
use DBUtils\FileDB;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
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
        $this->pdoConnection->executeStatement("CREATE TABLE user (
            id VARCHAR(36) PRIMARY KEY, 
            firstname VARCHAR(255) NOT NULL, 
            lastname VARCHAR(255) NOT NULL,
            vacationDays INT NOT NULL, 
            compensatoryTimeDays INT NOT NULL)");
        $this->pdoConnection->executeStatement("INSERT INTO user (id, firstname, lastname, vacationDays, compensatoryTimeDays) VALUES ('626e9b71-54f6-44fd-9539-0120cf37daf6', 'John', 'Doe', 25, 10)");
        $this->pdoConnection->executeStatement("INSERT INTO user (id, firstname, lastname, vacationDays, compensatoryTimeDays) VALUES ('35c5382b-04c8-4555-aa5c-d07631ef19b5', 'Jane', 'Doe', 22, 9)");
    }
    /**
     * @test
     */
    public function should_list_users_from_user_table()
    {
        $repository = new UserRepository($this->container->get('Connection'));
        $users = $repository->list();

        self::assertThat($users, self::countOf(2));
        self::assertThat($users[0], self::isInstanceOf(User::class));
    }

    /**
     * @test
     */
    public function should_get_user_from_id()
    {
        $repository = new UserRepository($this->container->get('Connection'));
        $user = $repository->get('626e9b71-54f6-44fd-9539-0120cf37daf6');

        self::assertThat($user, self::isInstanceOf(User::class));
        self::assertThat($user->getId(), self::equalTo('626e9b71-54f6-44fd-9539-0120cf37daf6'));
    }

    /**
     * @test
     */
    public function should_add_user_to_user_table()
    {
        $repository = new UserRepository($this->container->get('Connection'));

        $repository->add(new User('4ef3e75e-bdae-4fbe-8584-f21fbb39bb2f', 'Robert', 'Paulson'));

        $record = $this->pdoConnection
            ->executeQuery("SELECT id, firstname, lastname FROM user WHERE id ='4ef3e75e-bdae-4fbe-8584-f21fbb39bb2f'")
            ->fetchAssociative(\PDO::FETCH_ASSOC);
        self::assertThat($record['id'], self::equalTo('4ef3e75e-bdae-4fbe-8584-f21fbb39bb2f'));
        self::assertThat($record['firstname'], self::equalTo('Robert'));
        self::assertThat($record['lastname'], self::equalTo('Paulson'));
    }

    /**
     * @test
     */
    public function should_delete_user_from_user_table()
    {
        $repository = new UserRepository($this->container->get('Connection'));

        $affectedRows = $repository->delete('626e9b71-54f6-44fd-9539-0120cf37daf6');

        self::assertThat($affectedRows, self::equalTo(1));
        $records = $this->pdoConnection
            ->executeQuery("SELECT id, firstname, lastname, vacationDays, compensatoryTimeDays FROM user")
            ->fetchAllAssociative();
        self::assertThat($records, self::countOf(1));
    }

    /**
     * @test
     */
    public function should_throw_exception_on_integrity_constraint_violation()
    {
        $repository = new UserRepository($this->container->get('Connection'));

        $this->expectException(Exception::class);
        $repository->add(new User('35c5382b-04c8-4555-aa5c-d07631ef19b5', 'Robert', 'Paulson', 22, 5));
    }

    /**
     * @test
     */
    public function should_throw_exception_when_delete_unknown_user()
    {
        $repository = new UserRepository($this->container->get('Connection'));

        $affectedRows = $repository->delete('4ef3e75e-bdae-4fbe-8584-f21fbb39bb2f');

        self::assertThat($affectedRows, self::equalTo(0));
    }
}
