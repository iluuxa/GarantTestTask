<?php
namespace App\Controllers;
use Klein\Response;

abstract class Controller
{
    protected function sendJSON(mixed $value, int $code = 200):Response{
        $response = new Response(json_encode($value,JSON_UNESCAPED_UNICODE),$code);
        $response->header('Content-type','application/json');
        return $response;
    }

}