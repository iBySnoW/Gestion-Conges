<?php

namespace App\Repository;

use App\Model\Conge;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;

class CongeRepository
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * CongeRepository constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    public function add(Conge $conge): void
    {
        $this->connection->transactional(function ($conn) use ($conge) {
            $qb = $conn->createQueryBuilder();
            $qb->insert('conge')
                ->setValue('id', '?')
                ->setValue('employee', '?')
                ->setValue('startDate', '?')
                ->setValue('endDate', '?')
                ->setValue('type', '?')
                ->setValue('days', '?')

                ->setParameter(0, $conge->getId())
                ->setParameter(1, $conge->getEmployee())
                ->setParameter(2, $conge->getStartDate())
                ->setParameter(3, $conge->getEndDate())
                ->setParameter(4, $conge->getType())
                ->setParameter(5, $conge->getDays());
            $qb->execute();
        });
    }
    public function list(): ArrayCollection
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'employee','startDate', 'endDate', 'type', 'days')
            ->from('conge')
            ->executeQuery();

        return $this->mapList($statement->fetchAllAssociative());
    }
    public function get(string $employee): ArrayCollection
    {
       $SQL = $this->connection->createQueryBuilder()
            ->select('id','startDate', 'endDate', 'type', 'days')
            ->from('conge')
            ->where('employee = :employee')
            ->getSQL();
        $statement = $this->connection->prepare($SQL);
        $result = $statement->executeQuery(['employee'=>$employee]);
        $record = $result->fetchAllAssociative();
        return $this->mapList($record);
    }
    public function delete(string $id):int
    {
        $qb = $this->connection->createQueryBuilder()
            ->delete('conge')
            ->where('id = :id')
            ->setParameter('id', $id);
        return $qb->executeStatement();
    }
    private function mapList($records): ArrayCollection
    {
        $collections = new ArrayCollection($records);
        return $collections->map(function ($record) {
            return $this->map($record);
        });
    }
    private function map($record): Conge
    {
        return new Conge($record['id'], $record['employee'], $record['startDate'], $record['endDate'], $record['type'], $record['days']);
    }
    public function getConnection(){
        return $this->connection;
    }
}