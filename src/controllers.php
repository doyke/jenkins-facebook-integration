<?php

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

// May be hit after successful facebook authentication. The framework throws an error
// if this route is not defined.
$app->match('/login_check', function() use ($app) {
	return $app->redirect($app['url_generator']->generate('homepage'));
});

$app->error('controller.errorHandler:handleErrorAction');

return $app;
