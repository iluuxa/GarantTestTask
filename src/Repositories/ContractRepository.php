<?php

namespace App\Repositories;

use App\Application;
use App\Exceptions\ValidationException;
use Cassandra\Date;
use DateTime;
use Klein\Response;

class ContractRepository
{
    public function getContracts(int $id){
        $connection = Application::getConnection();
    }

    /**
     * @throws ValidationException
     */
    public function addContract(array $params):bool{
        $connection = Application::getConnection();
        $connection->beginTransaction();
        $checkClientQuery = $connection->prepare('SELECT EXISTS (SELECT * FROM clients where id=:client_id)');
        $checkClientQuery->execute(['client_id'=>$params['client_id']]);
        $clientExists = $checkClientQuery->fetch(\PDO::FETCH_NUM);
        if(!$clientExists[0]){
            $connection->rollBack();
            throw new ValidationException('Клиента с таким id ('.$params['client_id'].') не существует');
        }
        $format = 'Ymd';
        $checkContractsQuery = $connection->prepare('SELECT id, start, end FROM contracts where client_id=:client_id');
        $checkContractsQuery->execute(['client_id'=>$params['client_id']]);
        $contracts = $checkContractsQuery->fetchAll(\PDO::FETCH_ASSOC);
        dd($contracts);
        foreach ($contracts as $contract){
            $tempStart = DateTime::createFromFormat($format, $contract['start']);
            $tempEnd = DateTime::createFromFormat($format, $contract['start']);
            if((($params['start']>=$tempStart)&&($params['start']<=$tempEnd))||
                (($params['end']>=$tempStart)&&($params['end']<=$tempEnd))){
                $connection->rollBack();
                throw new ValidationException('Дата действия договора пересекается с договором id = '.$contract['id']);
            }
        }
        $params['start']=$params['start']->format($format);
        $params['end']=$params['end']->format($format);
        $columns='';
        $values='';
        foreach ($params as $key => $value){
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $key;
            $values .= ($values == '') ? '' : ', ';
            $values .= ':'.$key;
        }
        $sql = "INSERT INTO contracts ({$columns}) VALUES ({$values})";
        $query = $connection->prepare($sql);
        foreach ($params as $key => $value){
            $query->bindValue(":{$key}",$value);
        }
        $query->execute();
        $connection->commit();
        return true;
    }

}