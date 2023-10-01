<?php

namespace App\Repositories;

use App\Application;
use App\Exceptions\ValidationException;
use PDO;

class EmployeeRepository
{
    public function addEmployee(array $params): bool
    {
        $connection = Application::getConnection();
        $columns = '';
        $values = '';
        foreach ($params as $key => $value) {
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $key;
            $values .= ($values == '') ? '' : ', ';
            $values .= ':' . $key;
        }
        $sql = "INSERT INTO employees ({$columns}) VALUES ({$values})";
        $query = $connection->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue(":{$key}", $value);
        }
        return $query->execute();
    }

    public function getReport(array $params): array
    {
        $start = $params['start']->format('Ymd');
        $end = $params['end']->format('Ymd');
        $connection = Application::getConnection();
        $query = $connection->prepare("SELECT * FROM employees left join garant_db.contracts c on employees.id = c.employee_id AND c.start > '{$start}' AND c.start < '{$end}'");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
    }

    /**
     * @throws ValidationException
     */
    public function fireEmployee(int $id): bool
    {
        $connection = Application::getConnection();
        $connection->beginTransaction();
        $checkClientQuery = $connection->prepare('SELECT EXISTS (SELECT * FROM clients where id=:id)');
        $checkClientQuery->execute(['id' => $id]);
        $employeeExists = $checkClientQuery->fetch(PDO::FETCH_NUM);
        if (!$employeeExists[0]) {
            $connection->rollBack();
            throw new ValidationException('Сотрудника с таким id (' . $id . ') не существует');
        }
        $query = $connection->prepare('SELECT employees.id, employees.name, c.id, client_id, employee_id FROM employees left join garant_db.contracts c on employees.id = c.employee_id where employees.fired = 0');
        $query->execute();
        $employees = $query->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        $result = [];
        foreach ($employees as $employee_id => $employee) {
            //$result[$id]['id']=$id;
            $result[$employee_id]['clients'] = [];
            foreach ($employee as $contract) {
                if (intval($contract['client_id']) > 0) {
                    $result[$employee_id]['clients'][] = intval($contract['client_id']);
                }
            }
            $result[$employee_id]['clients'] = array_unique($result[$employee_id]['clients']);
            $result[$employee_id]['count'] = count($result[$employee_id]['clients']);
        }
        $query = $connection->prepare("UPDATE employees SET fired = 1 WHERE id = {$id}");
        $query->execute();
        $fired = $result[$id];
        unset($result[$id]);
        if (count($result) > 0) {
            foreach ($fired['clients'] as $client) {
                $counts = array_map(function ($item) {
                    return $item['count'];
                }, $result);
                $min = array_search(min($counts), $counts);
                $query = $connection->prepare("UPDATE contracts SET employee_id = {$min} WHERE client_id = {$client}");
                $query->execute();
                $result[$min]['count']++;
            }
        } else {
            foreach ($fired['clients'] as $client) {
                $query = $connection->prepare("UPDATE contracts SET employee_id = NULL WHERE client_id = {$client}");
                $query->execute();
            }
        }
        return $connection->commit();
    }

}