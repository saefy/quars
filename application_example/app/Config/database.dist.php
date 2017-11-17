<?php
/*; Configuracion de ambiente de base de datos*/

$dbconf = array(

	'DEVELOPMENT' => array(
		'db_host' => '127.0.0.1',
		'db_username' => 'dbuser',
		'db_password' => 'dbpass',
		'db_name' => 'dbname',
		'db_type' => 'mysqli'
	),

	'PRODUCTION' => array(
		'db_host' => '127.0.0.1',
		'db_username' => 'root',
		'db_password' => 'dbpass',
		'db_name' => 'dbname',
		'db_type' => 'mysqli',
	),

);

return $dbconf;
