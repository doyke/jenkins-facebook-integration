<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;
use FHJ\Entities\User;

/**
 * UserEditController
 * @package FHJ\Controllers
 */
class UserEditController extends BaseController {

	const ROUTE_USER_EDIT = 'userEdit';

	public function editAction(Request $request, User $user) {
		$form = $this->defineForm($user);
		$form->handleRequest($request);

		if ($form->isValid()) {
			return $this->processEdit($form, $user);
		}

		return $this->getTemplateEngine()->render('userEdit.html.twig', array(
			'form' => $form->createView(),
			'user' => $user
		));
	}

	private function defineForm(User $user) {
		$form = $this->getFormFactory()->createBuilder('form', $user);

		$form->add('email', 'genemu_plain', array(
			'label' => 'Email address'
		))->add('loginAllowed', 'checkbox', array(
				'label' => 'Login allowed?'
			))->add('projectCreationAllowed', 'checkbox', array(
				'label' => 'Project creation allowed?'
			))->add('admin', 'checkbox', array(
				'label' => 'Is administrator?'
			))->add('save', 'submit_cancel_combo', array(
				'label' => 'Save changes',
				'label_cancel' => 'Cancel',
				'href_cancel' => $this->generateRoute(UserListController::ROUTE_USER_LIST)
			));

		return $form->getForm();
	}

	private function processEdit(Form $form, User $originalUser) {
		try {
			// fetches the supplied User object which already has been modified
			$editedUser = $form->getData();

			$this->updateUser($originalUser, $editedUser);
		} catch (\Exception $e) {
			$this->getLogger()->addError(sprintf('error when saving edited user with id "%d"', $originalUser->getId()),
				array('exception' => $e));

			$this->getSession()->getFlashBag()->add('error', sprintf(
                'The edited user "%s" could not be saved to the database!', $originalUser->getEmail()));
            return $this->doRedirect(UserListController::ROUTE_USER_LIST);
        }

        $this->getSession()->getFlashBag()->add('success', sprintf('The user "%s" has been successfully edited!',
                $editedUser->getEmail()));
        return $this->doRedirect(UserListController::ROUTE_USER_LIST);
    }

	/**
	 * @param User $originalUser
	 * @param $editedUser
	 *
	 * @throws \RuntimeException
	 */
	private function updateUser(User $originalUser, $editedUser) {
		if ( !$originalUser->equals($editedUser) ) {
			throw new \RuntimeException(sprintf('user objects do not match: edited "%d" and original "%d"',
				$editedUser->getId(), $originalUser->getId()));
		}

		$this->getUserRepository()->updateUser($editedUser);
	}
}