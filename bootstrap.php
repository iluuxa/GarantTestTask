<?php

use App\Application;
use App\Config;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(APP_DIR);
$dotenv->load();
$config = (new Config(APP_DIR . '/config'))->load()->get('database');
Application::setConnection(new PDO("{$config['driver']}:host={$config['host']};dbname={$config['database']}", $config['username'], $config['password']));