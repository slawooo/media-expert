<?php

namespace App\Controller\Api;

use App\Entity\Record;
use App\Factory\RecordSearchCriteriaFactory;
use App\Mapper\RecordResponseMapper;
use App\Repository\RecordRepository;
use App\Service\RecordService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/records')]
class RecordController extends AbstractController
{
    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly RecordService $recordService,
        private readonly RecordResponseMapper $recordResponseMapper,
        private readonly RecordSearchCriteriaFactory $recordSearchCriteriaFactory,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'api_record_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $criteria = $this->recordSearchCriteriaFactory->createFromRequest($request);
        $records = $this->recordRepository->search($criteria);

        $responseData = [];
        foreach ($records as $record) {
            $responseData[] = $this->recordResponseMapper->map($record);
        }

        return $this->json($responseData);
    }

    #[Route('/{id}', name: 'api_record_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $record = $this->recordRepository->findOneWithStatusHistory($id);

        if (!$record instanceof Record) {
            return $this->json(
                ['message' => 'Record not found.'],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json($this->recordResponseMapper->map($record, true));
    }

    #[Route('', name: 'api_record_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(
                ['message' => 'Invalid JSON payload.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $number = $data['number'] ?? null;
        $status = $data['status'] ?? null;

        if (!is_string($number) || $number === '' || !is_string($status) || $status === '') {
            return $this->json(
                ['message' => 'Fields "number" and "status" are required.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $record = $this->recordService->create($number, $status);

        return $this->json(
            $this->recordResponseMapper->map($record, true),
            Response::HTTP_CREATED,
        );
    }

    #[Route('/{id}', name: 'api_record_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $record = $this->recordRepository->find($id);

        if (!$record instanceof Record) {
            return $this->json(
                ['message' => 'Record not found.'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(
                ['message' => 'Invalid JSON payload.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $number = $data['number'] ?? null;

        if (!is_string($number) || $number === '') {
            return $this->json(
                ['message' => 'Field "number" is required.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $record = $this->recordService->update($record, $number);

        return $this->json($this->recordResponseMapper->map($record, true));
    }

    #[Route('/{id}/status', name: 'api_record_change_status', methods: ['PATCH'])]
    public function changeStatus(int $id, Request $request): JsonResponse
    {
        $record = $this->recordRepository->findOneWithStatusHistory($id);

        if (!$record instanceof Record) {
            return $this->json(
                ['message' => 'Record not found.'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(
                ['message' => 'Invalid JSON payload.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $status = $data['status'] ?? null;

        if (!is_string($status) || $status === '') {
            return $this->json(
                ['message' => 'Field "status" is required.'],
                Response::HTTP_BAD_REQUEST,
            );
        }

        $record = $this->recordService->changeStatus($record, $status);

        return $this->json($this->recordResponseMapper->map($record, true));
    }

    #[Route('/{id}', name: 'api_record_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $record = $this->recordRepository->find($id);

        if (!$record instanceof Record) {
            return $this->json(
                ['message' => 'Record not found.'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $this->recordService->delete($record);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
