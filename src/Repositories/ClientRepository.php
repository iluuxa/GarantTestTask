<?php

namespace App\Repositories;

use App\Application;
use App\Entities\Client;
use PDOStatement;
use PDO;

class ClientRepository
{
    public function getGroupedClientList():array
    {
        $connection = Application::getConnection();
        $query = $connection->prepare("SELECT clients.id, clients.name, taxpayer_number, phone, c.id as contract_id, client_id, employee_id, start, end, sum, e.name as employee_name, birth_date, salary FROM clients left join garant_db.contracts c on clients.id = c.client_id left join garant_db.employees e on e.id = c.employee_id");
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_ASSOC);
    }

    public function getGroupedClientById(int $id):array{
        $connection = Application::getConnection();
        $query = $connection->prepare("SELECT clients.id, clients.name, taxpayer_number, phone, c.id as contract_id, client_id, employee_id, start, end, sum, e.name as employee_name, birth_date, salary FROM clients left join garant_db.contracts c on clients.id = c.client_id left join garant_db.employees e on e.id = c.employee_id where clients.id=:id");
        $query->execute(['id'=>$id]);
        return $query->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_ASSOC);
    }

    /**
     * В эту функцию передаются только валидные параметры, соответствующие названиям колонок таблицы клиентов
    */
    public function addClient(array $params):array{
        $connection = Application::getConnection();
        $sql = '';
        $columns='';
        $values='';
        foreach ($params as $key => $value){
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $key;
            $values .= ($values == '') ? '' : ', ';
            $values .= ':'.$key;
        }
        $sql = "INSERT INTO clients ({$columns}) VALUES ({$values})";
        $query = $connection->prepare($sql);
        foreach ($params as $key => $value){
            $query->bindValue(":{$key}",$value);
        }
        $query->execute();
        return $this->getGroupedClientById($connection->lastInsertId());
    }
}