<?php
/*
;-------------------------------
;         APLICACION
;-------------------------------
; Configuracion de Aplicaciones
; app_activated: Es el ambiente activo, ej: production_app
; default_language: Indica el lenguaje por defecto que es leido desde app/locale/{lenguaje}/
*/
$config = array(
	'APP' => array(
		'app_activated'		=> 'DEVELOPMENT_APP',
		'mod_rewrite'		=> true,
		'db_pwd_encripted'	=> false,
		'default_language'	=> 'es',
		'utf8_encode'		=> false,
	),
/*
;-------------------------------------------------
;         AMBIENTES: desarrollo, produccion, otro.
;-------------------------------------------------
; database_mode: Es el entorno del a base de datos,
;                configurado en app/conf/database.ini
; interactive:   Habilita los mensajes de errores tecnicos, excepciones e indicaciones
;                para el programador (Activelo para desarrollo).
; www_server:    Url Base del proyecto.
; debug:         Habilita librerias de debug (Activelo para desarrollo).
*/
	'DEVELOPMENT_APP' 	=> array(
		'database_mode' 	=> 'DEVELOPMENT',
		'www_server'		=> '',
		'interactive'		=> true,
		'debug'				=> true,
		'on_internet'		=> false,
		'allow_dev_tools' 	=> true,
		'server_os'			=> 'mac',
		'sync_enabled'		=> false,
		'fix_path'			=> true
	),
	'PRODUCTION_APP'	=> array(
		'database_mode' 	=> 'PRODUCTION',
		'www_server' 		=> '',
		'interactive' 		=> true,
		'debug' 			=> false,
		'on_internet' 		=> true,
		'server_os' 		=> 'linux',
		'on_production' 	=> true,
		'fix_path'			=> false
	)
);

return $config;
