<?php

namespace App\Conge;

use App\Controller\CongeController;
use App\Model\Conge;
use App\Repository\CongeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CongeControllerTest extends TestCase
{
    /**
     * @test
     */
    public function should_list_conge()
    {
        $repository = $this->createMock(CongeRepository::class);
        $repository->method('list')->willReturn(new ArrayCollection([
            new Conge('626e9b71-54f6-44fd-9539-0120cf37daf6', '626e9b71-54f6-44fd-9539-0120cf37daf6', '2022-01-01', '2022-01-04', "CP", 4),
            new Conge('35c5382b-04c8-4555-aa5c-d07631ef19b5', '626e9b71-54f6-44fd-9539-0120cf37daf6', '2022-08-21', '2022-08-22', "RTT", 2),
        ]));
        $controller = new CongeController($repository);

        $response = $controller->list();

        self::assertThat($response, self::isInstanceOf(JsonResponse::class));
        self::assertThat($response->getContent(),self::equalTo('[{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","startDate":"2022-01-01","endDate":"2022-01-04","type":"CP","days":4},{"id":"35c5382b-04c8-4555-aa5c-d07631ef19b5","startDate":"2022-08-21","endDate":"2022-08-22","type":"RTT","days":2}]'));
    }

    /**
     * @test
     */
    public function should_return_HTTP_status_201_add_conge()
    {
        $repository = $this->createMock(CongeRepository::class);
        $repository->method('add');
        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","startDate":"2022-01-01","endDate":"2022-01-04","type":"RTT"}');

        $controller = new CongeController($repository);

        $response = $controller->add($request);

        self::assertThat($response->getStatusCode(), self::equalTo(Response::HTTP_CREATED));
    }
}


