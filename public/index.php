<?php

use App\Controllers\ClientController;
use App\Controllers\ContractController;
use App\Controllers\EmployeeController;
use App\Repositories\ClientRepository;
use App\Repositories\ContractRepository;
use App\Repositories\EmployeeRepository;
use App\Services\ClientValidationService;
use App\Services\ContractValidationService;
use App\Services\EmployeeValidationService;
use Klein\Klein;

error_reporting(E_ERROR);
ini_set('display_errors', true);
define('APP_DIR', dirname(__DIR__));
const DATE_FORMAT = 'Y-m-d';
require_once APP_DIR . '/vendor/autoload.php';
require_once APP_DIR . '/bootstrap.php';

$klein = new Klein();
$klein->with('/api/v1/client', function () use ($klein) {
    $controller = new ClientController(new ClientRepository(), new ClientValidationService());
    $klein->respond('POST', '', [$controller, 'addClient']);
    $klein->respond('GET', '/[i:id]', [$controller, 'getClient']);
    $klein->respond('GET', '', [$controller, 'getList']);
    $klein->respond('GET', '/export', [$controller, 'exportClients']);
    $klein->respond('GET', '/form', [$controller, 'getForm']);
});
$klein->with('/api/v1/contract', function () use ($klein) {
    $controller = new ContractController(new ContractRepository(), new ContractValidationService());
    $klein->respond('POST', '/create', [$controller, 'addContract']);
    $klein->respond('POST', '/update', [$controller, 'updateContract']);
    $klein->respond('POST', '/delete', [$controller, 'deleteContract']);
    $klein->respond('GET', '', [$controller, 'getList']);
});
$klein->with('/api/v1/employee', function () use ($klein) {
    $controller = new EmployeeController(new EmployeeRepository(), new EmployeeValidationService());
    $klein->respond('POST', '', [$controller, 'addEmployee']);
    $klein->respond('GET', '/report', [$controller, 'getReport']);
    $klein->respond('POST', '/fire', [$controller, 'fireEmployee']);
});

$klein->dispatch();