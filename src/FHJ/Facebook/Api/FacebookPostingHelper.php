<?php

namespace FHJ\Facebook\Api;

/**
 * FacebookPostingHelper
 * @package FHJ\Facebook\Api
 */
class FacebookPostingHelper {

	/**
	 * @var \BaseFacebook
	 */
	private $facebook;

	public function __construct(\BaseFacebook $facebook) {
		$this->facebook = $facebook;
	}

	public function postSimpleMessageToGroup($groupId, $messageContent) {
		$this->postDataToGroup($groupId, array('message' => $messageContent));
	}

	public function postMessageWithLinkToGroup($groupId, $messageContent, $linkDestination) {
		$this->postDataToGroup($groupId, array(
			'message' => $messageContent,
			'link' => $linkDestination
		));
	}

	public function postDataToGroup($groupId, array $data) {
		$this->facebook->api(sprintf('/%s/feed', $groupId), 'POST', $data);
	}
	
} 