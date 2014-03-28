<?php

namespace FHJ\Listeners;

use Monolog\Logger;
use FHJ\Repositories\UserDbRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * UserAuthenticationListener
 *
 * Refreshes the extended access token upon user login.
 *
 * @package FHJ\Listeners
 */
class UserAuthenticationListener extends DefaultAuthenticationSuccessHandler {

	/**
	 * @var \BaseFacebook
	 */
	private $facebook;

	/**
	 * @var UserDbRepositoryInterface
	 */
	private $userRepository;

	/**
	 * @var Logger
	 */
	private $logger;

	public function setDependencies(\BaseFacebook $facebook, UserDbRepositoryInterface $userRepository,
	                                Logger $logger) {
		$this->facebook = $facebook;
		$this->userRepository = $userRepository;
		$this->logger = $logger;
	}

	/**
	 * This is called when an interactive authentication attempt succeeds. This
	 * is called by authentication listeners inheriting from
	 * AbstractAuthenticationListener.
	 *
	 * @param Request $request
	 * @param TokenInterface $token
	 *
	 * @throws \RuntimeException
	 * @return Response never null
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
		$user = $token->getUser();

		if (!$this->facebook->setExtendedAccessToken()) {
			throw new \RuntimeException('retrieval of extended auth token failed');
		}

		$user->setFacebookAccessToken($this->facebook->getAccessToken());
		$this->userRepository->updateUser($user);
		$this->logger->addInfo(sprintf('retrieved and saved extended auth token for user id "%d"', $user->getId()));

		return parent::onAuthenticationSuccess($request, $token);
	}

}
