<?php

namespace App\Controllers;

use App\Entities\Client;
use App\Repositories\ClientRepository;
use App\Services\ClientValidationService;
use Klein\Response;

class ClientController extends Controller
{

    /**
     * @param $clientRepository
     */
    public function __construct(private readonly ClientRepository        $clientRepository,
                                private readonly ClientValidationService $clientValidationService)
    {
    }

    public function getList(): Response
    {
        $clients = $this->clientRepository->getGroupedClientList();
        $result = [];
        /**
         * Массив сгруппирован по id клиента. Необходимо сгруппировать его по остальным полям клиента, а также выделить
         * контракты в массив-свойство клиента. Для этого написан находящийся ниже вложенный цикл.
         */
        foreach ($clients as $id => $client) {
            $result[$id]['name'] = $client[0]['name'];
            $result[$id]['phone'] = $client[0]['phone'];
            $result[$id]['taxpayer_number'] = $client[0]['taxpayer_number'];
            foreach ($client as $key => $contract) {
                ;
                if ($contract['contract_id'] !== null) {
                    $result[$id]['contracts'][$contract['contract_id']]['id'] = $contract['contract_id'];
                    $result[$id]['contracts'][$contract['contract_id']]['start'] = $contract['start'];
                    $result[$id]['contracts'][$contract['contract_id']]['end'] = $contract['end'];
                    $result[$id]['contracts'][$contract['contract_id']]['sum'] = $contract['sum'];
                    $result[$id]['contracts'][$contract['contract_id']]['employee']['id'] = $contract['employee_id'];
                    $result[$id]['contracts'][$contract['contract_id']]['employee']['employee_name'] = $contract['employee_name'];
                    $result[$id]['contracts'][$contract['contract_id']]['employee']['salary'] = $contract['salary'];
                    $result[$id]['contracts'][$contract['contract_id']]['employee']['birth_date'] = $contract['birth_date'];
                }
            }
        }
        return new Response(json_encode($result, JSON_UNESCAPED_UNICODE), 200);
    }

    public function getClient($request): Response
    {
        $client = $this->clientRepository->getGroupedClientById($request->id);
        $result = [];
        if (!empty($client)) {
            $result['id'] = $request->id;
            $result['name'] = $client[$request->id][0]['name'];
            $result['phone'] = $client[$request->id][0]['phone'];
            $result['taxpayer_number'] = $client[$request->id][0]['taxpayer_number'];
            foreach ($client[$request->id] as $key => $contract) {
                if ($contract['contract_id'] !== null) {
                    $result['contracts'][$contract['contract_id']]['id'] = $contract['contract_id'];
                    $result['contracts'][$contract['contract_id']]['start'] = $contract['start'];
                    $result['contracts'][$contract['contract_id']]['end'] = $contract['end'];
                    $result['contracts'][$contract['contract_id']]['sum'] = $contract['sum'];
                    $result['contracts'][$contract['contract_id']]['employee']['id'] = $contract['employee_id'];
                    $result['contracts'][$contract['contract_id']]['employee']['employee_name'] = $contract['employee_name'];
                    $result['contracts'][$contract['contract_id']]['employee']['salary'] = $contract['salary'];
                    $result['contracts'][$contract['contract_id']]['employee']['birth_date'] = $contract['birth_date'];
                }
            }
        }
        return new Response(json_encode($result, JSON_UNESCAPED_UNICODE), 200);
    }

    public function addClient($request): Response
    {
        try {
            return new Response(json_encode(
                    $this->clientRepository->addClient(
                        $this->clientValidationService->validateClient($request)
                    ), JSON_UNESCAPED_UNICODE
                ), 200
            );
        }catch (\Exception $e){
            return new Response(json_encode($e, JSON_UNESCAPED_UNICODE));
        }
    }
}