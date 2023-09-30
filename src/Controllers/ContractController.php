<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\ContractRepository;
use App\Services\ContractValidationService;
use Klein\Request;
use Klein\Response;

class ContractController
{

    public function __construct(private readonly ContractRepository $contractRepository, private readonly ContractValidationService $contractValidationService)
    {
    }

    public function addContract(Request $request): Response{

        try {
            return new Response(
                json_encode(
                    $this->contractRepository->addContract(
                        $this->contractValidationService->validateContract($request)
                    ), JSON_UNESCAPED_UNICODE
                ), 200
            );
        } catch (ValidationException $e) {
            return new Response(json_encode($e->getMessage(),JSON_UNESCAPED_UNICODE),$e->getCode());
        }
    }
}