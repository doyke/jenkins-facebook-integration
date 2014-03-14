<?php

if (!file_exists(__DIR__.'/../resources/config/prod.php')) {
	exit(0);
}

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__.'/../resources/config/prod.php';
require __DIR__.'/../src/app.php';

require __DIR__.'/../src/controllers.php';

$app->run();
