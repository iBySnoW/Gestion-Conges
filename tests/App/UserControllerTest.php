<?php

namespace App;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends TestCase
{
    /**
     * @test
     */
    public function should_list_users()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('list')->willReturn(new ArrayCollection([
            new User('626e9b71-54f6-44fd-9539-0120cf37daf6', 'John', 'Doe'),
            new User('35c5382b-04c8-4555-aa5c-d07631ef19b5', 'Jane', 'Doe'),
        ]));
        $controller = new UserController($repository);

        $response = $controller->list();

        self::assertThat($response, self::isInstanceOf(JsonResponse::class));
        self::assertThat($response->getContent(),self::equalTo('[{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","firstName":"John","lastName":"Doe"},{"id":"35c5382b-04c8-4555-aa5c-d07631ef19b5","firstName":"Jane","lastName":"Doe"}]'));
    }

    /**
     * @test
     */
    public function should_get_user_from_id()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('get')->willReturn(
            new User('626e9b71-54f6-44fd-9539-0120cf37daf6', 'John', 'Doe')
        );
        $controller = new UserController($repository);

        $response = $controller->get(1);

        self::assertThat($response, self::isInstanceOf(JsonResponse::class));
        self::assertThat($response->getContent(),self::equalTo('{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","firstName":"John","lastName":"Doe"}'));
    }

    /**
     * @test
     */
    public function should_return_HTTP_status_201_add_user()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('add');
        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","firstName":"John","lastName":"Doe"}');

        $controller = new UserController($repository);

        $response = $controller->add($request);

        self::assertThat($response->getStatusCode(), self::equalTo(Response::HTTP_CREATED));
    }

    /**
     * @test
     */
    public function should_return_HTTP_status_500__when_add_user_failed()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('add')->willThrowException(new Exception());
        $request = $this->createMock(Request::class);
        $request->method('getContent')->willReturn('{"id":"626e9b71-54f6-44fd-9539-0120cf37daf6","firstName":"John","lastName":"Doe"}');

        $controller = new UserController($repository);

        $response = $controller->add($request);

        self::assertThat($response->getStatusCode(), self::equalTo(Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    /**
     * @test
     */
    public function should_delete_user()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->method('delete')->willReturn(1);

        $controller = new UserController($repository);

        $response = $controller->delete('626e9b71-54f6-44fd-9539-0120cf37daf6');

        self::assertThat($response->getStatusCode(), self::equalTo(Response::HTTP_NO_CONTENT));
    }
}
