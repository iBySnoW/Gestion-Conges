<?php


namespace App;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController implements Controller
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(): JsonResponse
    {
        $response = new JsonResponse();
        $response->setData($this->repository->list()->toArray());
        return $response;
    }

    public function get(string $id): JsonResponse
    {
        return new JsonResponse($this->repository->get($id));
    }

    public function add(Request $request): JsonResponse
    {
        $user = $this->userMap($request->getContent());

        try {
            $this->repository->add($user);
        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse('', Response::HTTP_CREATED);
    }

    public function delete(string $id): JsonResponse
    {
        $this->repository->delete($id);
        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    private function userMap(string $json)
    {
        $user = json_decode($json);
        return new User(
            $user->id,
            $user->firstName,
            $user->lastName,
        );
    }


}