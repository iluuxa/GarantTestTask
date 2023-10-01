<?php

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Repositories\ClientRepository;
use App\Services\ClientValidationService;
use Klein\Request;
use Klein\Response;
use libphonenumber\NumberParseException;

class ClientController extends Controller
{

    /**
     * @param ClientRepository $clientRepository
     * @param ClientValidationService $clientValidationService
     */
    public function __construct(private readonly ClientRepository        $clientRepository,
                                private readonly ClientValidationService $clientValidationService)
    {
    }

    public function getList(Request $request): Response
    {
        $sort = $request->param('sort', 1);
        $by = $request->param('by', 'id');
        $limit = $request->param('limit', 1000);
        $params = $this->clientValidationService->validateGetParams(['sort' => $sort, 'by' => $by, 'limit' => $limit]);
        $clients = $this->clientRepository->getGroupedClientList($params['sort'], $params['by'], $params['limit']);
        $result = [];
        /**
         * Массив сгруппирован по id клиента. Необходимо сгруппировать его по остальным полям клиента, а также выделить
         * контракты в массив-свойство клиента. Для этого написан находящийся ниже вложенный цикл.
         */
        foreach ($clients as $id => $client) {
            $result[$id]['id'] = $id;
            $result[$id]['name'] = $client[0]['name'];
            $result[$id]['phone'] = $client[0]['phone'];
            $result[$id]['taxpayer_number'] = $client[0]['taxpayer_number'];
            foreach ($client as $contract) {
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
        return $this->sendJSON($result);
    }

    public function getClient(Request $request): Response
    {
        try {
            $id = $this->clientValidationService->validateClientId($request);
        } catch (ValidationException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
        $client = $this->clientRepository->getGroupedClientById($id);
        $result = [];
        if (!empty($client)) {
            $result['id'] = $id;
            $result['name'] = $client[$id][0]['name'];
            $result['phone'] = $client[$id][0]['phone'];
            $result['taxpayer_number'] = $client[$id][0]['taxpayer_number'];
            foreach ($client[$id] as $contract) {
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
        return $this->sendJSON($result);
    }

    public function addClient(Request $request): Response
    {
        try {
            return $this->sendJSON($this->clientRepository->addClient($this->clientValidationService->validateClient($request)));
        } catch (ValidationException|NumberParseException $e) {
            return $this->sendJSON($e->getMessage(), $e->getCode());
        }
    }

    public function exportClients(): Response
    {
        $clients = $this->clientRepository->getListClientsOnly();
        $out = fopen('php://output', 'w');
        foreach ($clients as $fields) {
            fputcsv($out, $fields);
        }
        fclose($out);
        $response = new Response();
        $response->header('Content-Type', 'application/octet-stream');
        $response->header('Content-disposition', 'attachment; filename=clients.csv');
        return $response;
    }

    public function getForm(): Response
    {
        return new Response($this->renderTemplate('index.html'));
    }
}