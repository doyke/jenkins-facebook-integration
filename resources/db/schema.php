<?php

$schema = new \Doctrine\DBAL\Schema\Schema();

$users = $schema->createTable('users');
$users->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$users->addColumn('facebook_id', 'string', array('length' => 30, 'notnull' => true));
$users->addColumn('email', 'string', array('length' => 60));
$users->addColumn('realname', 'string', array('length' => 60));
$users->addColumn('is_login_allowed', 'boolean', array('default' => 0));
$users->addColumn('is_admin', 'boolean', array('default' => 0));
$users->setPrimaryKey(array('id'));
$users->addUniqueIndex(array('facebook_id'));

$projects = $schema->createTable('projects');
$projects->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$projects->addColumn('user_id', 'integer', array('unsigned' => true));
$projects->addColumn('is_enabled', 'boolean');
$projects->addColumn('facebook_group_id', 'string', array('length' => 30));
$projects->addColumn('secret_key', 'string', array('length' => 60));
$projects->addColumn('svnplot_db_path', 'string', array('length' => 255));
$projects->setPrimaryKey(array('id'));
$projects->addForeignKeyConstraint($users, array('user_id'), array('id'));

return $schema;
