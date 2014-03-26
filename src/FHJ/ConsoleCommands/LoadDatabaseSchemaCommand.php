<?php

namespace FHJ\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\DBAL\Connection;

/**
 * LoadDatabaseSchemaCommand
 * @package FHJ\ConsoleCommands
 */
class LoadDatabaseSchemaCommand extends Command {

    /**
     * @var string
     */
    private $schemaPath;

    /**
     * @var Connection
     */
    private $database;
    
    public function __construct($schemaPath, Connection $database) {
        $this->schemaPath = $schemaPath;
        $this->database = $database;
    }

	protected function configure() {
	    $this->setName('doctrine:schema:load')
             ->setDescription('Load schema into the configured database');
	}

    protected function execute(InputInterface $input, OutputInterface $output) {
        $schema = require($this->schemaPath);

        foreach ($schema->toSql($this->database->getDatabasePlatform()) as $sql) {
            $this->database->exec($sql.';');
        }
        
        $output->writeln('<info>Finished writing schema into database</info>');
	}

} 