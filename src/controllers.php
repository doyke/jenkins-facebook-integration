<?php

use Symfony\Component\HttpFoundation\Response;

$app->match('/',                            'controller.homepage:indexAction')
	->bind('homepage');

$app->match('/projects',                    'controller.projectList:listOwnAction')
    ->bind('projects')
    ->secure('ROLE_USER');
$app->match('/projects/all',                'controller.projectList:listAllAction')
    ->bind('projectsAll')
    ->secure('ROLE_ADMIN');
$app->match('/projects/{project}/delete',   'controller.projectDelete:deleteAction')
	->assert('project', '\d+')
	->convert('project', 'converter.project:convert')
	->bind('projectDelete')
    ->secure('ROLE_USER');
$app->match('/projects/{project}/edit',     'controller.projectEdit:editAction')
	->assert('project', '\d+')
	->convert('project', 'converter.project:convert')
	->bind('projectEdit')
    ->secure('ROLE_USER');
$app->match('/projects/new',                'controller.projectEdit:newAction')
    ->bind('projectNew')
    ->secure('ROLE_USER');

$app->match('/users',                       'controller.userList:listAllAction')
    ->bind('users')
    ->secure('ROLE_ADMIN');
$app->match('/users/{user}/delete',         'controller.userDelete:deleteAction')
	->assert('user', '\d+')
	->convert('user', 'converter.user:convert')
	->bind('userDelete')
    ->secure('ROLE_ADMIN');
$app->match('/users/{user}/edit',           'controller.userEdit:editAction')
	->assert('user', '\d+')
	->convert('user', 'converter.user:convert')
	->bind('userEdit')
    ->secure('ROLE_ADMIN');

$app->match('/login_check', function() use ($app) {
	$user = $app['facebook']->api('/me');

	return 'Welcome ' . $user['name'];
});

$app->error(function (\Exception $e, $code) use ($app) {
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
