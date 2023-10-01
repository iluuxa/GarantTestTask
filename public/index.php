<?php

use App\Application;
use \App\Controllers\ClientController;
use App\Controllers\ContractController;
use App\Controllers\EmployeeController;
use App\Repositories\ClientRepository;
use App\Repositories\ContractRepository;
use App\Repositories\EmployeeRepository;
use App\Services\ClientValidationService;
use App\Services\ContractValidationService;
use App\Services\EmployeeValidationService;
use Klein\Request;
error_reporting(E_ERROR);
ini_set('display_errors', true);
define('APP_DIR', dirname(__DIR__));
require_once APP_DIR . '/vendor/autoload.php';
require_once APP_DIR . '/bootstrap.php';

$klein = new \Klein\Klein();
$klein->with('/api/v1/client', function () use($klein){
    $controller = new ClientController(new ClientRepository(), new ClientValidationService());
    $klein->respond('POST','',[$controller,'addClient']);
    $klein->respond('GET','/[i:id]',[$controller,'getClient']);
    $klein->respond('GET', '', [$controller,'getList']);
    $klein->respond('GET','/export',[$controller,'exportClients']);
});
$klein->with('/api/v1/contract', function () use($klein){
    $controller = new ContractController(new ContractRepository(), new ContractValidationService());
    $klein->respond('POST','/create',[$controller,'addContract']);
    $klein->respond('POST','/update',[$controller,'updateContract']);
    $klein->respond('POST','/delete',[$controller,'deleteContract']);
});
$klein->with( '/api/v1/employee', function () use ($klein){
    $controller = new EmployeeController(new EmployeeRepository(),new EmployeeValidationService());
    $klein->respond('POST','',[$controller,'addEmployee']);
});

/*try {
    $klein->respond('GET', '/api/v1/client', function(){
        $response = (new ClientController(new \App\repositories\ClientRepository()))->getList();
        $response->send();;
    });
} catch (\Psr\Container\NotFoundExceptionInterface $e) {
} catch (\Psr\Container\ContainerExceptionInterface $e) {
}
$klein->respond('GET','/api/v1/client/export',$controller->getList());
$klein->respond('POST','/api/v1/employee',$controller->getList());*/
$klein->dispatch();
//$response = Application::run(Request::createFromGlobals());

//$response->send();