<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use Klein\Request;

class EmployeeValidationService extends ValidationService
{
    /**
     * @throws ValidationException
     */
    public function validateEmployee(Request $request): array
    {
        $params = json_decode($request->body(), true);
        $result = [];
        if (isset($params['name'])) {
            $result['name'] = $params['name'];
        }
        if (isset($params['birth_date'])) {
            $result['birth_date'] = $this->validateDate($params['birth_date'])->format(DATE_FORMAT);
        }
        if (isset($params['salary'])) {
            if (!is_numeric($params['salary']) || $params['salary'] < 0) {
                throw new ValidationException('Неверно указана зарплата: ' . $params['salary']);
            }
            $result['salary'] = $params['salary'];
        }
        return $result;
    }

    /**
     * @throws ValidationException
     */
    public function validateReport(Request $request): array
    {
        $params = ['start'=>$request->paramsGet()->get('start'),'end'=>$request->paramsGet()->get('end')];
        if (isset($params['start'])) {
            $result['start'] = $this->validateDate($params['start']);
        } else {
            throw new ValidationException('Нет даты начала периода (start)');
        }
        if (isset($params['end'])) {
            $result['end'] = $this->validateDate($params['end']);
        } else {
            throw new ValidationException('Нет даты конца периода (end)');
        }
        if ($result['end'] < $result['start']) {
            throw new ValidationException('Дата начала не может быть позже даты конца периода');
        }
        return $result;
    }

    /**
     * @throws ValidationException
     */
    public function validateEmployeeId(Request $request): int
    {
        $params = json_decode($request->body(), true);
        if (intval($params['id']) > 0) {
            return intval($params['id']);
        } else {
            throw new ValidationException('Неверный id сотрудника');
        }
    }
}