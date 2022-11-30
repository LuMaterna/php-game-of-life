<?php

use DI\Container;
use Life\RunGameCommand;
use Symfony\Component\Console\Application;

require './vendor/autoload.php';

$app = new Application();
$container = new Container();
$command = $container->get(RunGameCommand::class);
$app->add($command);
$app->run();
