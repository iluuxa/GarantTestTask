<?php

namespace App\repositories;

use App\Application;
use App\entities\Client;
use PDOStatement;

class ClientRepository
{
    public function getList():array
    {
        $connection = Application::getConnection();
        $query = $connection->prepare("SELECT clients.id,clients.name, sum, e.name FROM clients left join garant_db.contracts c on clients.id = c.client_id left join garant_db.employees e on e.id = c.employee_id");
        $query->execute();
        $clients = $query->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_ASSOC);
        $result = [];
        foreach ($clients as $id => $client) {
            var_dump($id);
            var_dump($client);
            foreach ($client as $key => $contract){
                //TODO: contracts array
            }
            //$result[] = new Client($id,$client['name'],$client[]);
        }
        return $clients;
    }
}