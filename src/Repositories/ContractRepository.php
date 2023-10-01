<?php

namespace App\Repositories;

use App\Application;
use App\Exceptions\ValidationException;
use DateTime;
use PDO;


class ContractRepository
{
    /**
     * @throws ValidationException
     */
    private function checkEmployee(PDO $connection, int $employee_id): void
    {
        $checkEmployeeQuery = $connection->prepare('SELECT EXISTS (SELECT * FROM employees where id=:employee_id)');
        $checkEmployeeQuery->execute(['employee_id' => $employee_id]);
        $employeeExists = $checkEmployeeQuery->fetch(PDO::FETCH_NUM);
        if (!$employeeExists[0]) {
            $connection->rollBack();
            throw new ValidationException('Сотрудника с таким id (' . $employee_id . ') не существует');
        }
    }

    /**
     * @throws ValidationException
     */
    private function checkClient(PDO $connection, int $client_id): void
    {
        $checkClientQuery = $connection->prepare('SELECT EXISTS (SELECT * FROM clients where id=:client_id)');
        $checkClientQuery->execute(['client_id' => $client_id]);
        $clientExists = $checkClientQuery->fetch(PDO::FETCH_NUM);
        if (!$clientExists[0]) {
            $connection->rollBack();
            throw new ValidationException('Клиента с таким id (' . $client_id . ') не существует');
        }
    }

    /**
     * @throws ValidationException
     */
    private function checkContract(PDO $connection, int $contract_id): void
    {
        $checkClientQuery = $connection->prepare('SELECT EXISTS (SELECT * FROM contracts where id=:contract_id)');
        $checkClientQuery->execute(['contract_id' => $contract_id]);
        $contractExists = $checkClientQuery->fetch(PDO::FETCH_NUM);
        if (!$contractExists[0]) {
            $connection->rollBack();
            throw new ValidationException('Контракта с таким id (' . $contract_id . ') не существует');
        }
    }

    /**
     * @throws ValidationException
     */
    public function addContract(array $params): bool
    {
        $connection = Application::getConnection();
        $connection->beginTransaction();
        if (isset($params['employee_id'])) {
            $this->checkEmployee($connection, $params['employee_id']);
        }
        $this->checkClient($connection, $params['client_id']);
        $checkContractsQuery = $connection->prepare('SELECT id, start, end FROM contracts where client_id=:client_id');
        $checkContractsQuery->execute(['client_id' => $params['client_id']]);
        $contracts = $checkContractsQuery->fetchAll(PDO::FETCH_ASSOC);
        foreach ($contracts as $contract) {
            $tempStart = DateTime::createFromFormat(DATE_FORMAT, $contract['start'])->setTime(0, 0);
            $tempEnd = DateTime::createFromFormat(DATE_FORMAT, $contract['end'])->setTime(0, 0);
            if ((($params['start'] >= $tempStart) && ($params['start'] <= $tempEnd)) ||
                (($params['end'] >= $tempStart) && ($params['end'] <= $tempEnd)) ||
                (($params['start'] <= $tempStart) && ($params['end'] >= $tempEnd))
            ) {
                $connection->rollBack();
                throw new ValidationException('Дата действия договора пересекается с договором id = ' . $contract['id']);
            }
        }
        $params['start'] = $params['start']->format(DATE_FORMAT);
        $params['end'] = $params['end']->format(DATE_FORMAT);
        $columns = '';
        $values = '';
        foreach ($params as $key => $value) {
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $key;
            $values .= ($values == '') ? '' : ', ';
            $values .= ':' . $key;
        }
        $sql = "INSERT INTO contracts ({$columns}) VALUES ({$values})";
        $query = $connection->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue(":{$key}", $value);
        }
        $query->execute();
        return $connection->commit();
    }

    /**
     * @throws ValidationException
     */
    public function updateContract(int $id, array $params): bool
    {
        $connection = Application::getConnection();
        $connection->beginTransaction();
        $this->checkContract($connection, $id);
        if (isset($params['employee_id'])) {
            $this->checkEmployee($connection, $params['employee_id']);
        }
        $this->updateContractByParams($connection, $id, $params);
        return $connection->commit();
    }

    private function updateContractByParams(PDO $connection, int $id, array $params): void
    {
        $columns = '';
        foreach ($params as $column => $value) {
            $columns .= ($columns == '') ? '' : ', ';
            $columns .= $column . ' = :' . $column;
        }
        $sql = 'UPDATE contracts SET ' . $columns . ' WHERE id = :id;';
        $query = $connection->prepare($sql);
        foreach ($params as $column => $value) {
            $query->bindValue(":{$column}", $value);
        }
        $query->bindValue(":id", $id);
        $query->execute();
    }

    /**
     * @throws ValidationException
     */
    public function updateContractWithCheck(int $id, array $params): bool
    {
        $connection = Application::getConnection();
        $connection->beginTransaction();
        $this->checkContract($connection, $id);
        if (isset($params['client_id'])) {
            $this->checkClient($connection, $params['client_id']);
        } else {
            $getClientQuery = $connection->prepare('SELECT client_id FROM contracts WHERE id=:id');
            $getClientQuery->execute(['id' => $id]);
            $client = $getClientQuery->fetch()[0];
        }
        if (isset($params['employee_id'])) {
            $this->checkEmployee($connection, $params['employee_id']);
        }
        $checkContractsQuery = $connection->prepare('SELECT id, start, end FROM contracts WHERE client_id=:client_id');
        $checkContractsQuery->execute(['client_id' => $client]);
        $contracts = $checkContractsQuery->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
        if (!isset($params['start'])) {
            $params['start'] = DateTime::createFromFormat(DATE_FORMAT, $contracts[$id][0]['start'])->setTime(0, 0, 0, 0);
        }
        if (!isset($params['end'])) {
            $params['end'] = DateTime::createFromFormat(DATE_FORMAT, $contracts[$id][0]['end'])->setTime(0, 0, 0, 0);
        }
        if ($params['end'] < $params['start']) {
            throw new ValidationException('Начало действия контракта не может быть позже его конца');
        }
        foreach ($contracts as $key => $contract) {
            if ($key == $id) {
                continue;
            }
            $tempStart = DateTime::createFromFormat(DATE_FORMAT, $contract[0]['start'])->setTime(0, 0, 0, 0);
            $tempEnd = DateTime::createFromFormat(DATE_FORMAT, $contract[0]['end'])->setTime(0, 0, 0, 0);
            if ((($params['start'] >= $tempStart) && ($params['start'] <= $tempEnd)) ||
                (($params['end'] >= $tempStart) && ($params['end'] <= $tempEnd)) ||
                (($params['start'] <= $tempStart) && ($params['end'] >= $tempEnd))
            ) {
                $connection->rollBack();
                throw new ValidationException('Дата действия договора пересекается с договором id = ' . $key);
            }
        }
        $params['start'] = $params['start']->format(DATE_FORMAT);
        $params['end'] = $params['end']->format(DATE_FORMAT);
        $this->updateContractByParams($connection, $id, $params);
        return $connection->commit();
    }

    /**
     * @throws ValidationException
     */
    public function deleteContract(int $id): bool
    {
        $connection = Application::getConnection();
        $connection->beginTransaction();
        $this->checkContract($connection, $id);
        $query = $connection->prepare('DELETE FROM contracts WHERE id = :id');
        $query->execute(['id' => $id]);
        return $connection->commit();
    }

    public function getList(): array
    {
        $connection = Application::getConnection();
        $query = $connection->prepare('Select * from contracts');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}