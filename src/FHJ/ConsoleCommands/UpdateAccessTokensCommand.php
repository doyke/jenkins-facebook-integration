<?php

namespace FHJ\ConsoleCommands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Monolog\Logger;
use FHJ\Repositories\UserDbRepositoryInterface;
use FHJ\Entities\User;
use FHJ\Facebook\Api\EndpointFactory;
use FHJ\Facebook\Data\FacebookDataRetriever;

/**
 * UpdateAccessTokensCommand
 * @package FHJ\ConsoleCommands
 */
class UpdateAccessTokensCommand extends Command {

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var UserDbRepositoryInterface
     */
    private $userRepository;
    
    /**
     * @var EndpointFactory
     */
    private $endpointFactory;
    
    public function __construct(UserDbRepositoryInterface $userRepository, Logger $logger) {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        
        $this->endpointFactory = new EndpointFactory();
    }

	protected function configure() {
	    $this->setName('social:accesstokens:update')
	         ->setDescription('Update all Facebook access tokens to prolong validity');
	}

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Fetching all users from database');
        $users = $this->userRepository->findAllUsers();
        
        foreach ($users as $user) {
            $output->writeln(sprintf('Processing user "%s"', $user->getEmail()));
            $this->processUser($user, $output);
        }
        
        $output->writeln('Finished updating of access tokens');
	}
	
	private function processUser(User $user, OutputInterface $output) {
		$facebook = $this->endpointFactory->getFacebookApi($user);
		
		// get some "dummy data" from Facebook to ensure that the token is refreshed
	    $dataRetriever = new FacebookDataRetriever($facebook, $this->logger);
	    $foundEmail = $dataRetriever->getEmail();
	    
	    if ($user->getEmail() !== $foundEmail) {
	        $this->logger->addError(sprintf('update access tokens: user "%d" had email mismatch ("%s" <> "%s")',
	            $user->getId(), $user->getEmail(), $foundEmail);
	        
	        $output->writeln(sprintf('Email mismatch for user: "%s" <> "%s"'$user->getEmail(), $foundEmail);
	        
	        return;
	    }
	    
	    $user->setFacebookAccessToken($facebook->getAccessToken());
	    $this->userRepository->updateUser($user);
	}

} 