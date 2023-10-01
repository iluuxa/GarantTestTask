<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use Klein\Request;

class EmployeeValidationService
{
    /**
     * @throws ValidationException
     */
    public function validateEmployee(Request $request): array
    {
        $params = json_decode($request->body(), true);
        $result = [];
        $dateFormat = 'Y-m-d';
        if (isset($params['name'])) {
            $result['name'] = $params['name'];
        }
        if (isset($params['birth_date'])) {
            $result['birth_date'] = \DateTime::createFromFormat($dateFormat, $params['birth_date']);
            if(!$result['birth_date']){
                throw new ValidationException('Некорректная дата: ' . $params['birth_date'] . '. Корректный формат даты: ГГГГ-ММ-ДД.');
            }
            $result['birth_date']=$result['birth_date']->setTime(0,0,0,0);
            if ($result['birth_date']->format($dateFormat) == $params['birth_date']) {
                throw new ValidationException('Некорректная дата: ' . $params['birth_date'] . '. Корректный формат даты: ГГГГ-ММ-ДД.');
            }
        }
        if (isset($params['salary'])){
            if(!is_numeric($params['salary'])||$params['salary']<0){
                throw new ValidationException('Неверно указана зарплата: '.$params['salary']);
            }
            $result['salary']=$params['salary'];
        }
        return $result;
    }
}