<?php


namespace App;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;

class UserRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * UserRepository constructor.
     */
    public function __construct(Connection $connection)
    {

        $this->connection = $connection;
    }

    public function add(User $user)
    {
        $this->connection->transactional(function ($conn) use ($user) {
            $qb = $conn->createQueryBuilder();
            $qb->insert('user')
                ->setValue('id', '?')
                ->setValue('firstname', '?')
                ->setValue('lastname', '?')
                ->setParameter(0, $user->getId())
                ->setParameter(1, $user->getFirstName())
                ->setParameter(2, $user->getLastName());
            $qb->execute();
        });
    }

    public function list()
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'firstname', 'lastname')
            ->from('user')
            ->executeQuery();
        return $this->mapList($statement->fetchAllAssociative());
    }

    public function get(string $id)
    {
        $SQL = $this->connection->createQueryBuilder()
            ->select('id', 'firstname', 'lastname')
            ->from('user')
            ->where('id = :id')
            ->getSQL();
        $statement = $this->connection->prepare($SQL);
        $result = $statement->executeQuery(['id' => $id]);
        $record = $result->fetchAssociative();
        return $this->map($record);
    }

    public function delete(string $id):int
    {
        $qb = $this->connection->createQueryBuilder()
            ->delete('user')
            ->where('id = :id')
            ->setParameter('id', $id);
        return $qb->executeStatement();
    }

    private function mapList($records)
    {
        $collections = new ArrayCollection($records);
        return $collections->map(function ($record) {
            return $this->map($record);
        });
    }

    private function map($record)
    {
        return new User($record['id'], $record['firstname'], $record['lastname']);
    }
}
