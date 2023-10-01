<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use Klein\Request;

class ContractValidationService
{
    /**
     * @throws ValidationException
     */
    public function validateContractAdd(Request $request):array{
        $params = json_decode($request->body(), true);
        $result = [];
        $dateFormat = 'Y-m-d';
        if(!isset($params['client_id'])){
            throw new ValidationException('Не введён id клиента (client_id)');
        }
        if(intval($params['client_id'])<=0){
            throw new ValidationException('Неверно введён id клиента');
        }
        $result['client_id']=$params['client_id'];
        if(!isset($params['start'])){
            throw new ValidationException('Не введена дата начала действия договора ("start")');
        }
        $start = \DateTime::createFromFormat($dateFormat,$params['start']);
        if(!$start || $params['start']!=$start->format($dateFormat)){
            throw new ValidationException('Некорректная дата: '.$params['start'].'. Корректный формат даты: ГГГГ-ММ-ДД.');
        }
        if(!isset($params['end'])){
            throw new ValidationException('Не введена дата концы действия договора ("end")');
        }
        $end = \DateTime::createFromFormat($dateFormat,$params['end']);
        if(!$end || $params['end']!=$end->format($dateFormat)){
            throw new ValidationException('Некорректная дата: '.$params['end'].'. Корректный формат даты: ГГГГ-ММ-ДД.');
        }
        if($end<$start){
            throw new ValidationException('Начало действия контракта не может быть позже его конца');
        }
        if(isset($params['sum'])){
            if (!is_numeric($params['sum'])){
                throw new ValidationException('Сумма должна быть числом.');
            }
            if ($params['sum']<0){
                throw new ValidationException('Сумма не может быть отрицательной.');
            }
            $result['sum']=$params['sum'];
        }
        if(isset($params['employee_id'])){
            if(intval($params['employee_id'])<=0){
                throw new ValidationException('Неверно введён id сотрудника');
            }
            $result['employee_id']=$params['employee_id'];
        }
        $result['start']=$start->setTime(0,0,0,0);
        $result['end']=$end->setTime(0,0,0,0);;
        return $result;
    }

    /**
     * @throws ValidationException
     */
    public function validateContractUpdate(Request $request)
    {
        $params = json_decode($request->body(), true);
        $needsIntersectionCheck = false;
        $result = [];
        $dateFormat = 'Y-m-d';
        if(!isset($params['id'])){
            throw new ValidationException('Не указан id договора');
        }
        if(isset($params['client_id'])){
            if (intval($params['client_id'])<=0){
            throw new ValidationException('Неверно введён id клиента');
            }
            $result['client_id']=$params['client_id'];
            $needsIntersectionCheck=true;
        }
        if(isset($params['start'])){
            $start = \DateTime::createFromFormat($dateFormat,$params['start']);
            if(!$start || $params['start']!=$start->format($dateFormat)){
                throw new ValidationException('Некорректная дата: '.$params['start'].'. Корректный формат даты: ГГГГ-ММ-ДД.');
            }
            $result['start']=$start->setTime(0,0,0,0);
            $needsIntersectionCheck=true;
        }
        if(isset($params['end'])){
            $end = \DateTime::createFromFormat($dateFormat,$params['end']);
            if(!$end || $params['end']!=$end->format($dateFormat)){
                throw new ValidationException('Некорректная дата: '.$params['end'].'. Корректный формат даты: ГГГГ-ММ-ДД.');
            }
            $result['end']=$end->setTime(0,0,0,0);
            $needsIntersectionCheck=true;
        }
        if(isset($params['start'])&&isset($params['end'])&&($end<$start)){
            throw new ValidationException('Начало действия контракта не может быть позже его конца');
        }
        if(isset($params['sum'])){
            if (!is_numeric($params['sum'])){
                throw new ValidationException('Сумма должна быть числом.');
            }
            if ($params['sum']<0){
                throw new ValidationException('Сумма не может быть отрицательной.');
            }
            $result['sum']=$params['sum'];
        }
        if(isset($params['employee_id'])){
            if(intval($params['employee_id'])<=0){
                throw new ValidationException('Неверно введён id сотрудника');
            }
            $result['employee_id']=$params['employee_id'];
        }

        return ['result'=>$result,'check'=>$needsIntersectionCheck,'id'=>$params['id']];
    }

}