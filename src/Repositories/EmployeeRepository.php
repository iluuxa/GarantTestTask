<?php

namespace App\Repositories;

use App\Application;

class EmployeeRepository
{
    public function addEmployee(array $params):bool{
        $connection = Application::getConnection();
        $columns='';
        $values='';
        foreach ($params as $key => $value){
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $key;
            $values .= ($values == '') ? '' : ', ';
            $values .= ':'.$key;
        }
        $sql = "INSERT INTO employees ({$columns}) VALUES ({$values})";
        $query = $connection->prepare($sql);
        foreach ($params as $key => $value){
            $query->bindValue(":{$key}",$value);
        }
        return $query->execute();
    }
}