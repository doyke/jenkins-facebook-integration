<?php

namespace FHJ\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SilexAssetic\Assetic\Dumper;

/**
 * AsseticDumpCommand
 * @package FHJ\ConsoleCommands
 */
class AsseticDumpCommand extends Command {

    /**
     * @var boolean
     */
    private $asseticEnabled;

    /**
     * @var Dumper
     */
    private $asseticDumper;
    
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;
    
    public function __construct($asseticEnabled, Dumper $asseticDumper, \Twig_Environment $twigEnvironment = null) {
        $this->asseticEnabled = $asseticEnabled;
        $this->asseticDumper = $asseticDumper;
        $this->twigEnvironment = $twigEnvironment;
    }

	protected function configure() {
	    $this->setName('assetic:dump')
             ->setDescription('Dumps all assets to the filesystem');
	}

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!$this->asseticEnabled) {
            return false;
        }

        $dumper = $this->asseticDumper;
        if ($this->twigEnvironment !== null) {
            $dumper->addTwigAssets();
        }
        
        $dumper->dumpAssets();
        $output->writeln('<info>Dump finished</info>');
	}

} 