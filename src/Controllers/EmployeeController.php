<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeValidationService;
use Klein\Request;
use Klein\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{

    public function __construct(private readonly EmployeeRepository        $employeeRepository,
                                private readonly EmployeeValidationService $employeeValidationService)
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

    public function fireEmployee(Request $request): Response
    {
        try {
            return $this->sendJSON(
                $this->employeeRepository->fireEmployee(
                    $this->employeeValidationService->validateEmployeeId($request)
                )
            );
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
    }

    public function getReport(Request $request): Response
    {
        try {
            $params = $this->employeeValidationService->validateReport($request);
            $employees = $this->employeeRepository->getReport($params);
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
        $result = [];
        $interval = $params['end']->diff($params['start'], true);
        $months = $interval->y * 12 + $interval->m;
        foreach ($employees as $id => $employee) {
            $result[$id]['id'] = $id;
            $result[$id]['name'] = $employee[0]['name'];
            $result[$id]['salary'] = (double)($employee[0]['salary']) * ($months);
            $result[$id]['sum'] = 0;
            $result[$id]['num'] = 0;

            foreach ($employee as $contract) {
                if (intval($contract['sum']) > 0) {
                    $result[$id]['sum'] += intval($contract['sum']);
                    $result[$id]['num']++;
                }
            }
        }
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1','id');
        $activeWorksheet->setCellValue('B1','ФИО');
        $activeWorksheet->setCellValue('C1','Зарплата');
        $activeWorksheet->setCellValue('D1','Сумма контрактов');
        $activeWorksheet->setCellValue('E1','Кол-во контрактов');
        $activeWorksheet->fromArray($result, '', 'A2', true);

        $writer = new Xlsx($spreadsheet);
        ob_start();
        try {
            $writer->save('php://output');
        } catch (Exception $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
        $excelOutput = ob_get_clean();
        //'efficiency'.$params['start'].'—'.$params['end']. '.xlsx'
        $response = new Response($excelOutput, 200);
        $response->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->header('Content-disposition', 'attachment; filename=' . 'efficiency' . $params['start']->format(DATE_FORMAT) . '--' . $params['end']->format(DATE_FORMAT) . '.xlsx');
        return $response;
    }

}