<?php
/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */
use Cake\Datasource\ConnectionManager;

require dirname(dirname(dirname(__DIR__))) . '/config/bootstrap.php';

if (!getenv('db_dsn')) {
    putenv('db_dsn=sqlite:///:memory:');
}
ConnectionManager::drop('test');
ConnectionManager::config('test', ['url' => getenv('db_dsn')]);
