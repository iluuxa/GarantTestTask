<?php

use App\Application;
use \App\Controllers\ClientController;
use App\Repositories\ClientRepository;
use App\Services\ClientValidationService;
use Klein\Request;
error_reporting(E_ALL);
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