<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\ContractRepository;
use App\Services\ContractValidationService;
use Klein\Request;
use Klein\Response;

class ContractController extends Controller
{

    public function __construct(private readonly ContractRepository $contractRepository, private readonly ContractValidationService $contractValidationService)
    {
    }

    public function addContract(Request $request): Response
    {

        try {
            return $this->sendJSON($this->contractRepository->addContract(
                $this->contractValidationService->validateContractAdd($request)
            ), JSON_UNESCAPED_UNICODE);
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
    }

    public function updateContract(Request $request): Response
    {
        try {
            $validated = $this->contractValidationService->validateContractUpdate($request);
            if ($validated['check']) {
                return $this->sendJSON($this->contractRepository->updateContractWithCheck($validated['id'], $validated['result']));
            } else {
                return $this->sendJSON($this->contractRepository->updateContract($validated['id'], $validated['result']));
            }
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
    }

    public function deleteContract(Request $request): Response
    {
        $params = json_decode($request->body(), true);
        if (isset($params['id']) && (intval($params['id']) > 0)) {
            try {
                return $this->sendJSON($this->contractRepository->deleteContract(intval($params['id'])));
            } catch (ValidationException $e) {
                return $this->sendJSON($e->getMessage(), $e->getCode());
            }
        } else {
            return $this->sendJSON(['message' => 'Неверно указан id'], 401);
        }
    }
}