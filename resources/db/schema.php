<?php

$schema = new \Doctrine\DBAL\Schema\Schema();

$users = $schema->createTable('users');
$users->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$users->addColumn('facebook_id', 'string', array('length' => 30, 'notnull' => true));
$users->addColumn('facebook_access_token', 'string', array('length' => 100));
$users->addColumn('facebook_access_expiration', 'datetime');
$users->addColumn('email', 'string', array('length' => 60));
$users->addColumn('is_login_allowed', 'boolean', array('default' => 0));
$users->addColumn('is_admin', 'boolean', array('default' => 0));
$users->setPrimaryKey(array('id'));
$users->addUniqueIndex(array('facebook_id'));

$projects = $schema->createTable('projects');
$projects->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$projects->addColumn('user_id', 'integer', array('unsigned' => true));
$projects->addColumn('is_enabled', 'boolean');
$projects->addColumn('facebook_group_id', 'string', array('length' => 30));
$projects->addColumn('secret_key', 'string', array('length' => 60, 'default' => ''));
$projects->addColumn('svnplot_db_path', 'string', array('length' => 255, 'default' => ''));
$projects->addColumn('title', 'string', array('length' => 100));
$projects->addColumn('description', 'string', array('length' => 600, 'default' => ''));
$projects->setPrimaryKey(array('id'));
$projects->addForeignKeyConstraint($users, array('user_id'), array('id'));

return $schema;
