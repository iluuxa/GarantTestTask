<?php

namespace App\Controllers;

use Klein\Response;

abstract class Controller
{
    protected function sendJSON(mixed $value, int $code = 200): Response
    {
        $response = new Response(json_encode($value, JSON_UNESCAPED_UNICODE), $code);
        $response->header('Content-type', 'application/json');
        return $response;
    }

    protected function renderTemplate(string $template): string
    {
        ob_start();
        include APP_DIR . '/templates/' . $template;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

}