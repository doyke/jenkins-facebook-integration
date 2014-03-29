<?php

namespace FHJ\Framework;

use Monolog\Logger;
use FHJ\Repositories\UserDbRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * UserAuthenticationHandler
 *
 * Refreshes the extended access token upon user login.
 *
 * @package FHJ\Listeners
 */
class UserAuthenticationHandler extends DefaultAuthenticationSuccessHandler {

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
		$this->logger->addDebug(sprintf('auth success for user id "%d" -> facebook id "%s", extending access token',
			$user->getId(), $this->facebook->getUser()));

		if ($this->facebook->getUser() == 0) {
			throw new \RuntimeException('facebook user is not valid (not logged in)');
		}

		$this->facebook->setExtendedAccessToken();

		$user->setFacebookAccessToken($this->facebook->getAccessToken());
		$this->userRepository->updateUser($user);
		$this->logger->addInfo(sprintf('retrieved and saved extended auth token for user id "%d"', $user->getId()));

		return parent::onAuthenticationSuccess($request, $token);
	}

}
