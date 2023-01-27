<?php


namespace App\Model;

class User implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $id;
    /**
     * @var string
     */
    private string $firstName;
    /**
     * @var string
     */
    private string $lastName;

    /**
     * @var int
     */
    private int $vacationDays;
    /**
     * @var int
     */
    private int $compensatoryTimeDays;

    public function __construct(string $id, string $firstName, string $lastName, int $vacationDays, int $compensatoryTimeDays)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $vacationDays > 25 ? $this->vacationDays = 25 : $this->vacationDays = $vacationDays;
        $compensatoryTimeDays > 10 ?  $this->compensatoryTimeDays = 10 : $this->compensatoryTimeDays = $compensatoryTimeDays;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getVacationDays(): int
    {
        return $this->vacationDays;
    }
    public function getCompensatoryTimeDays(): int
    {
        return $this->compensatoryTimeDays;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'vacationDays' => $this->vacationDays,
            'compensatoryTimeDays' => $this->compensatoryTimeDays,
        ];
    }
}