<?php

use Symfony\Component\Console\Application;
use FHJ\ConsoleCommands\UpdateAccessTokensCommand;
use FHJ\ConsoleCommands\AsseticDumpCommand;
use FHJ\ConsoleCommands\ClearCacheCommand;
use FHJ\ConsoleCommands\ShowDatabaseSchemaCommand;
use FHJ\ConsoleCommands\LoadDatabaseSchemaCommand;

$console = new Application('Jenkins Facebook Integration', '1.0');
$schemaFilepath = __DIR__ . '/../resources/db/schema.php';

$app->boot();

$console->add(new UpdateAccessTokensCommand($app['repository.users'], $app['monolog']));

$console->add(new AsseticDumpCommand($app['assetic.enabled'], $app['assetic.dumper'], 
    isset($app['twig']) ? $app['twig'] : null));

if (isset($app['cache.path'])) {
    $console->add(new ClearCacheCommand($app['cache.path']));
}

$console->add(new ShowDatabaseSchemaCommand($schemaFilepath, $app['db']));
$console->add(new LoadDatabaseSchemaCommand($schemaFilepath, $app['db']));

return $console;
