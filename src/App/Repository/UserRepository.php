<?php


namespace App\Repository;


use App\Model\Conge;
use App\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function add(User $user): void
    {
        $this->connection->transactional(function ($conn) use ($user) {
            $qb = $conn->createQueryBuilder();
            $qb->insert('user')
                ->setValue('id', '?')
                ->setValue('firstname', '?')
                ->setValue('lastname', '?')
                ->setValue('vacationDays', '?')
                ->setValue('compensatoryTimeDays', '?')

                ->setParameter(0, $user->getId())
                ->setParameter(1, $user->getFirstName())
                ->setParameter(2, $user->getLastName())
                ->setParameter(3, $user->getVacationDays())
                ->setParameter(4, $user->getCompensatoryTimeDays());
            $qb->execute();
        });
    }

    public function list(): ArrayCollection
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'firstname', 'lastname', 'vacationDays', 'compensatoryTimeDays')
            ->from('user')
            ->executeQuery();
        return $this->mapList($statement->fetchAllAssociative());
    }

    public function get(string $id): User
    {
        $SQL = $this->connection->createQueryBuilder()
            ->select('id', 'firstname', 'lastname', 'vacationDays', 'compensatoryTimeDays')
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

    public function updateVacationDays(string $id, int $newDaysCount):int
    {
        $qb = $this->connection->createQueryBuilder()
            ->update('user')
            ->where('id = :id')
            ->set('vacationDays', ':newDaysCount')
            ->setParameter('id', $id)
            ->setParameter('newDaysCount', $newDaysCount);
        return $qb->executeStatement();
    }
    public function updatecompensatoryTimeDays(string $id, int $newDaysCount):int
    {
        $qb = $this->connection->createQueryBuilder()
            ->update('user')
            ->where('id = :id')
            ->set('compensatoryTimeDays', ':newDaysCount')
            ->setParameter('id', $id)
            ->setParameter('newDaysCount', $newDaysCount);
        return $qb->executeStatement();
    }
    private function mapList($records): ArrayCollection
    {
        $collections = new ArrayCollection($records);
        return $collections->map(function ($record) {
            return $this->map($record);
        });
    }
    private function map($record): User
    {
        return new User($record['id'], $record['firstname'], $record['lastname'], $record['vacationDays'], $record['compensatoryTimeDays']);
    }

    public function updateDayCounterUser(Conge $conge): bool
    {
        $user = $this->get($conge->getEmployee());

        if ($conge->getType() === "CP") {
            if ($user->getVacationDays() - $conge->getDays() >= 0) {
                $newDaysCount = $user->getVacationDays() - $conge->getDays();
                $this->updateVacationDays($conge->getEmployee(), $newDaysCount);
               return true;
            }
        } else  {
            if ($user->getCompensatoryTimeDays() - $conge->getDays() >= 0) {
                $newDaysCount = $user->getCompensatoryTimeDays() - $conge->getDays() ;
                $this->updatecompensatoryTimeDays($conge->getEmployee(), $newDaysCount);
                return true;
            }
        }
        return false;
    }
}
