<?php

namespace FHJ\Converters;

use FHJ\Repositories\ProjectDbRepositoryInterface;
use Monolog\Logger;

/**
 * ProjectConverter
 * @package FHJ\Converters
 */
class ProjectConverter extends AbstractConverter {

	/**
	 * @var ProjectDbRepositoryInterface
	 */
	private $repository;

	public function __construct(ProjectDbRepositoryInterface $userRepository, Logger $logger) {
		parent::__construct($logger);

		$this->repository = $userRepository;
	}

	public function convert($value) {
		$repository = $this->repository;

		return $this->handleConversion($value, function($theProjectId) use ($repository) {
			return $repository->findProjectById($theProjectId);
		});
	}

} 