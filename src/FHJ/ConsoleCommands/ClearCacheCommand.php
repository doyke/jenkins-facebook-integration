<?php

namespace FHJ\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * ClearCacheCommand
 * @package FHJ\ConsoleCommands
 */
class ClearCacheCommand extends Command {

    /**
     * @var string
     */
    private $cachePath;
    
    public function __construct($cachePath) {
        $this->cachePath = $cachePath;
    }

	protected function configure() {
	    $this->setName('cache:clear')
             ->setDescription('Clears the cache');
	}

    protected function execute(InputInterface $input, OutputInterface $output) {
        $finder = Finder::create()->in($this->cachePath)->notName('.gitkeep');

        $filesystem = new Filesystem();
        $filesystem->remove($finder);

        $output->writeln(sprintf("%s <info>succeeded</info>", 'cache:clear'));
	}

} 