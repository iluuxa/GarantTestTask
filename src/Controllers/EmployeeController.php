<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeValidationService;
use Klein\Request;
use Klein\Response;
use libphonenumber\NumberParseException;

class EmployeeController extends Controller
{

    public function __construct(private readonly EmployeeRepository $employeeRepository, private readonly EmployeeValidationService $employeeValidationService)
    {
    }

    public function addEmployee(Request $request): Response
    {
        try {
            return $this->sendJSON(
                $this->employeeRepository->addEmployee(
                    $this->employeeValidationService->validateEmployee($request)
                ), JSON_UNESCAPED_UNICODE
            );
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
    }

}