<?php
use FHJ\Controllers\HomepageController;
use FHJ\Controllers\UserListController;
use FHJ\Controllers\UserDeleteController;
use FHJ\Controllers\UserEditController;
use FHJ\Controllers\ProjectListController;
use FHJ\Controllers\ProjectDeleteController;
use FHJ\Controllers\ProjectEditController;
use Symfony\Component\HttpFoundation\Response;

$app->match('/',                            'controller.homepage:indexAction')
	->bind(HomepageController::ROUTE_HOMEPAGE);

$app->match('/projects',                    'controller.projectList:listOwnAction')
    ->bind(ProjectListController::ROUTE_PROJECT_LIST_OWN)
    ->secure('ROLE_USER')
    ->onlyIfProjectsAllowed();
$app->match('/projects/all',                'controller.projectList:listAllAction')
    ->bind(ProjectListController::ROUTE_PROJECT_LIST_ALL)
    ->secure('ROLE_ADMIN');
$app->match('/projects/{project}/delete',   'controller.projectDelete:deleteAction')
	->assert('project', '\d+')
	->convert('project', 'converter.project:convert')
	->bind(ProjectDeleteController::ROUTE_PROJECT_DELETE)
    ->secure('ROLE_USER')
    ->onlyIfProjectsAllowed()
    ->onlyProjectAllowAccessIfAdminOrOwner();
$app->match('/projects/{project}/edit',     'controller.projectEdit:editAction')
	->assert('project', '\d+')
	->convert('project', 'converter.project:convert')
	->bind(ProjectEditController::ROUTE_PROJECT_EDIT)
    ->secure('ROLE_USER')
    ->onlyIfProjectsAllowed()
    ->onlyProjectAllowAccessIfAdminOrOwner();
$app->match('/projects/new',                'controller.projectEdit:newAction')
    ->bind(ProjectEditController::ROUTE_PROJECT_NEW)
    ->secure('ROLE_USER')
    ->onlyIfProjectsAllowed();

$app->match('/users',                       'controller.userList:listAllAction')
    ->bind(UserListController::ROUTE_USER_LIST)
    ->secure('ROLE_ADMIN');
$app->match('/users/{user}/delete',         'controller.userDelete:deleteAction')
	->assert('user', '\d+')
	->convert('user', 'converter.user:convert')
	->bind(UserDeleteController::ROUTE_USER_DELETE)
    ->secure('ROLE_ADMIN');
$app->match('/users/{user}/edit',           'controller.userEdit:editAction')
	->assert('user', '\d+')
	->convert('user', 'converter.user:convert')
	->bind(UserEditController::ROUTE_USER_EDIT)
    ->secure('ROLE_ADMIN');

// May be hit after successful facebook authentication. The framework throws an error
// if this route is not defined.
$app->match('/login_check', function() use ($app) {
	return $app->redirect($app['url_generator']->generate('homepage'));
});

$app->error(function(\Exception $e, $code) use ($app) {
	$app['monolog']->addError(sprintf('An exception occured: %s', $e->getMessage()), array('exception' => $e));

	if ($app['debug']) {
		return;
	}

	switch ($code) {
		case 404:
			$message = 'The requested page could not be found.';
			break;
		default:
			$message = 'We are sorry, but something went terribly wrong.';
	}

	return new Response($message, $code);
});

return $app;
