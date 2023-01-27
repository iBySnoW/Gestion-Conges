<?php

namespace App\Conge;

use App\Model\Conge;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CongeTest extends TestCase
{   
    /**
     * @test
     */
    public function should_return_1_when_starDate_and_endDate_is_equal() {
        $conge = new Conge('626e9b71-54f6-44fd-9539-0120cf37daf7', '626e9b71-54f6-44fd-9539-0120cf37dgh4', '2022-06-10', '2022-06-10', 'CP', 6);
        $numberOfDays = $conge->getNumberOfDays(new DateTimeImmutable($conge->getStartDate()), new DateTimeImmutable($conge->getEndDate()));

        self::assertThat($numberOfDays, self::equalTo(1));
    }

    /**
     * @test
     */
    public function should_return_2_when_starDate_and_endDate_have_1_day_difference() {
        $conge = new Conge('626e9b71-54f6-44fd-9539-0120cf37daf7', '626e9b71-54f6-44fd-9539-0120cf37dgh4', '2022-06-13', '2022-06-14', 'CP', 6);
        $numberOfDays = $conge->getNumberOfDays(new DateTimeImmutable($conge->getStartDate()), new DateTimeImmutable($conge->getEndDate()));
        self::assertThat($numberOfDays, self::equalTo(2));
    }

    /**
     * @test
     */
    public function should_return_4_when_starDate_and_endDate_have_3_day_difference() {
        $conge = new Conge('626e9b71-54f6-44fd-9539-0120cf37daf7', '626e9b71-54f6-44fd-9539-0120cf37dgh4', '2022-06-13', '2022-06-16', 'CP', 6);
        $numberOfDays = $conge->getNumberOfDays(new DateTimeImmutable($conge->getStartDate()), new DateTimeImmutable($conge->getEndDate()));
        self::assertThat($numberOfDays, self::equalTo(4));
    }

    /**
     * @test
     */
    public function should_return_5_for_one_week()
    {
        $conge = new Conge('626e9b71-54f6-44fd-9539-0120cf37daf7', '626e9b71-54f6-44fd-9539-0120cf37dgh4', '2022-06-11', '2022-06-18', 'CP', 6);
        $numberOfDays = $conge->getNumberOfDays(new DateTimeImmutable($conge->getStartDate()), new DateTimeImmutable($conge->getEndDate()));
        self::assertThat($numberOfDays, self::equalTo(5));
    }
}
