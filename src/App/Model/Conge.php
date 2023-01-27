<?php

namespace App\Model;

use Exception;
use DateTimeImmutable;

class Conge implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $id;
    /**
     * @var string|null
     */
    private string|null $employee;
    /**
     * @var string
     */
    private string $startDate;
    /**
     * @var string
     */
    private string $endDate;
    /**
     * @var string
     */
    private string $type;
    /**
     * @var int
     */
    private int $days;
    /**
     * @throws Exception
     */
    public function __construct(string $id, string|null $employee, string $startDate, string $endDate, string $type, int $days)
    {
        $this->id = $id;
        $this->employee = $employee;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
        if ($days == 0){
            $this->days = $this->getNumberOfDays(new DateTimeImmutable($startDate)  , new DateTimeImmutable($endDate));
        }else {
            $this->days = $days;
        }
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getEmployee(): string
    {
        return $this->employee;
    }
    public function getStartDate(): string
    {
        return $this->startDate;
    }
    public function getEndDate(): string
    {
        return $this->endDate;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getDays(): int
    {
        return $this->days;
    }
    public function getNumberOfDays(DateTimeImmutable $startDate, DateTimeImmutable $endDate): int
    {
        $days = 0;
        if($startDate == $endDate){
            return 1;
        }
        for ($date = $startDate; $date <= $endDate; $date = $date->modify('+1 day')) {
            // Obtenir le jour de la semaine
            $day_of_week = $date->format('l');
            // Vérifie si c'est un samedi ou un dimanche
            if ($day_of_week != "Saturday" && $day_of_week != "Sunday") {
                $days += 1;
            }
        }
        return $days;
    }

    public function getpublicHoliday($yearStart, $yearEnd) {
        /*Methode non terminée, et qui n'est donc non utilisée*/
        $yearStart = '2022';
        $yearEnd = '2023';
        $apiKey = '02ad51dc5ae82ed36568fb33bc839afa785440f9';
        $country = 'FR';
        $date = [];
        $listDate = [];
        if ($yearStart === $yearEnd) {
            $api_url = 'https://calendarific.com/api/v2/holidays?&api_key=' . $apiKey . '&country=' . $country . '&year=' . $yearStart;
            $json_data = file_get_contents($api_url);
            $response = json_decode($json_data);
            echo $response;
            $listDate = array_map(fn($value): mixed => $value->date, $response->response->holidays);
            $date = array_map(fn($value): mixed => $value->datetime->year . "-" . $value->datetime->month . "-" . $value->datetime->day, $listDate);
        }
        else {
            $year2 = $yearEnd;
            $api_url = 'https://calendarific.com/api/v2/holidays?&api_key=' . $apiKey . '&country=' . $country . '&year=' . $yearEnd;
            $response = file_get_contents($api_url);
            $listDate2 = array_map(fn($value): mixed => $value->date, json_decode($response)->response->holidays);
            $date2 = array_map(fn($value): mixed => $value->datetime->year . "-" . $value->datetime->month . "-" . $value->datetime->day, $listDate);
            $date = array_merge($date, $date2);
        }
        echo($date);
    }

    public function jsonSerialize(): array
    {
        if ($this->employee == null){
            return [
                'id' => $this->id,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'type' => $this->type,
                'days' => $this->days,
            ];
        }
        return [
            'id' => $this->id,
            'employee' => $this->employee,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'type' => $this->type,
            'days' => $this->days,
        ];
    }
}