<?php

namespace App\controllers;

use App\entities\Client;
use App\repositories\ClientRepository;
use Klein\Response;

class ClientController extends Controller
{

    private ClientRepository $clientRepository;

    /**
     * @param $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getList(): Response{
        $clients = $this->clientRepository->getList();
        return new Response(json_encode($clients,JSON_UNESCAPED_UNICODE),200);
    }

    public function getClient($request):Response{
        return new Response();
    }

    public function addClient($request):Response{
        return new Response();
    }
}