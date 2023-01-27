<?php

namespace App\Controller;

use App\Controller;
use App\Model\Conge;
use App\Repository\CongeRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CongeController implements Controller
{
    /**
     * @var CongeRepository
     */
    private CongeRepository $repository;

    public function __construct(CongeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(): JsonResponse
    {
        $response = new JsonResponse();
        $response->setData($this->repository->list()->toArray());
        return $response;
    }

    public function get(string $employee): JsonResponse
    {
        return new JsonResponse($this->repository->get($employee)->toArray());
    }

    public function add(Request $request): JsonResponse
    {
        $conge = $this->congerMap($request->getContent());
        $userRepository = new UserRepository($this->repository->getConnection());
        try {

            $result = $userRepository->updateDayCounterUser($conge);

            if($result){
                $this->repository->add($conge);
            }else {
                return new JsonResponse("This employee can't take so much days !", Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse('', Response::HTTP_CREATED);
    }

    public function delete(string $id): JsonResponse
    {

        try {
            $this->repository->delete($id);
        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    private function congerMap(string $json): Conge
    {
        $conge = json_decode($json);
        return new Conge(
            Uuid::uuid4()->toString(),
            $conge->employee,
            $conge->startDate,
            $conge->endDate,
            $conge->type,
            0
        );
    }


}