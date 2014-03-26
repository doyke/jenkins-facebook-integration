<?php

namespace FHJ\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

/**
 * ShowDatabaseSchemaCommand
 * @package FHJ\ConsoleCommands
 */
class ShowDatabaseSchemaCommand extends Command {

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
	    $this->setName('doctrine:schema:show')
             ->setDescription('Output schema declaration');
	}

    protected function execute(InputInterface $input, OutputInterface $output) {
        $schema = require($this->schemaPath);

        foreach ($schema->toSql($this->database->getDatabasePlatform()) as $sql) {
            $output->writeln($sql.';');
        }
        
        $output->writeln('-- Finished dump');
	}

} 