<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

class Quars {

	static $version = '0.3.4';

	/**
	 *@package Quars
	 *@method _use()
	 *@desc  Includes file or files inside Quars Application. Using it You Don't have
	 *		 to worry about relative path. Automatically detects the path.
	 *@example 1) Quars::_use('app/models/*'); Includes all files on app/models/
	 *         2) Quars::_use('app/models/*','.php'); Includes all .php files on app/models/
	 *         3) Quars::_use('app/models/file.php'); Includes app/models/file.php
	 *         4) Quars::_use('app/models/file.php', FALSE); includes  app/models/file.php but
	 *            if file does not exist the application can continue.
	 *            By default Required param is set to TRUE.
	 *
	 * */
	public static function _use($p_rute,$end = '',$required = true, $default_file=''){

		$x_r=explode('/',$p_rute);
		$num_dirs=count($x_r);
		$dir = trim(SYSTEM_PATH_QRS.$p_rute,'*');

		if($x_r[$num_dirs-1]!="*"){
			// un solo archivos
			try{
			 if(file_exists($dir)){
			 	include_once($dir);
			 }else{
			 	// Mandar error si es requerido
			 	if($default_file!==''){
			 		self::_use($default_file, '',false);
			 	}else{
				 	if($required == true){
				 		throw new QrsException('El archivo '.$dir.' no existe');
				 	}	
			 	}
			 	
			 }
			}catch(QrsException $ex){
				$ex->show();
			}

		}else{

			// multiples archivos
			try{
				if(!@$reader = opendir($dir)){


					throw new QrsException('Directorio '.$dir.' no existe');
				}
			}catch(QrsException $ex){
				$ex->show();
			}
			while ($file = readdir($reader)){

				if(!is_dir(SYSTEM_PATH_QRS.$file)){

					if($end != ''){
						// con terminacion
				  $filex =	explode($end, $file);
				  if(count($filex)==2 && $filex[1] == '' ){
				  	if(file_exists($dir.$file)){ include_once($dir.$file);}
				  }
					}else{
						// archivo completo
						if(file_exists($dir.$file)){
							include_once($dir.$file);
					 }
					}
				} // end if !is_dir
			} // end while
			closedir ($reader);
			// multiples archivos
		}

	} // end _use()
	/**
	 *@package Quars
	 *@method _use()
	 *@desc  Includes Plugin
	 *       1) loads app/plugins/{plugin}/{plugin}.class.php file &
	 *        app/plugins/{plugin}/{plugin}.utils.php file
	 * */
	public static function usePlugin($plugin){

		// Include Class
		self :: _use("app/plugins/".$plugin."/".$plugin.".class.php");

		// get other variables (css code, css links, js code, js links )
		//Js
		self :: _use("app/plugins/".$plugin."/".$plugin.".utils.php");
			
			
			
	} // _useControl('FkGrid')
	/**
	 *@package Quars
	 *@method url()
	 *@desc  returns the http url
	 * */
	//function para imprimir una url,
	//TODO:considerara permalinks en la proxima version
	public static function url($p_path){
		return RUTA_HTTP.$p_path;
	} // url

	/**
	 *@package Quars
	 *@method get_theme_dir()
	 *@desc  Returns the complete theme url
	 * */
	//function para obtener la ruta http de la ruta raiz del tema configurado
	public static function get_theme_dir($tema = NULL){

		if(!defined('SELECTED_THEME_DIR')){
			// definir ruta del tema seleccionado
			if($tema==NULL || trim($tema)==''){
				$the_theme=THEME;
			}else{
				$the_theme=$tema;
			}

			$url_dir = RUTA_HTTP.THEMES_DIR.'/'.$the_theme;
			define('SELECTED_THEME_DIR',$url_dir);

		}

		$ruta_http_tema = constant('SELECTED_THEME_DIR');

		return $ruta_http_tema;
	} // get_theme_dir
	/**
	 *@package Quars
	 *@method GetResource($Resource,$Type)
	 *@desc  Gets the .js or .css resource
	 * */
	public static function GetResource($Resource,$Type){
		$Resource = decode($Resource);

		$fileName = SYSTEM_PATH_QRS.$Resource;
		// Reject if is not  .js o .css file
		$ext=self::file_ext($fileName);
		if($ext=='js' || $ext == 'css'){
			if(file_exists($fileName)){

				if($Type=='js'){
					header("content-type: text/javascript");
					echo '/* File:'.$fileName.' */
';
					echo file_get_contents($fileName);
				}
				if($Type=='css'){
					header("Content-type: text/css");
					echo '/* File:'.$fileName.' */
';
					echo file_get_contents($fileName);
				}

			}else{echo '/* File not found:'.$fileName.' */';}
			
		}
	}
	/**
	 *@package Quars
	 *@method file_ext($file)
	 *@desc  returns the file exencion
	 * */
	public static function file_ext($file){
		$fext = explode('.',$file);
		$ext_pos = count($fext) - 1;
		return $fext[$ext_pos];
	}
	/**
	 *@package Quars
	 *@method Run()
	 *@desc  runs Quars 
	 **/
	public static function Run($path = null){

		//load view
		if ($path === null) {
			$path = fk_get_path();
		}

		$url_rs = self::url_processor($path);
		
		$controller_exist = false;
		if( file_exists(SYSTEM_PATH_QRS.'app/Controllers/'.$url_rs['file_controller']) ){ $controller_exist = true; }

		if($controller_exist==true){
			// controler existe

			// view existe
			//EJECUTAR CONTROLLER

			require(SYSTEM_PATH_QRS.'app/Controllers/'.$url_rs['file_controller']);

			$controller_class = '\\App\\Controllers\\'.$url_rs['controller'];
			//EJECUTAR CONTROLLER
			$page = new $controller_class($url_rs);

		}else{
			// controler no existe
			if($GLOBALS['QRS']['RUNNING']['app']['interactive']==true){
				// MOSTRAR ERROR Y AYUDA
				$folder = $url_rs['controller_name'];
				$cont_control = str_replace(array('__ControlerName__','__FolderName__'),array($url_rs['controller'],$folder),file_get_contents( dirname(__FILE__) .'/Blueprints/controller.tpl'));

				try{
					throw new QrsException('El Controlador "'.$url_rs['file_controller'].'" no existe');
				}catch(QrsException $e){
					$e->description='Es requerido el archivo Controllador <b>'.$url_rs['file_controller'].'</b>
						                    , sin embargo no fue encontrado.';
					if(app_running_is('allow_dev_tools', 1)){
						$e->solution='1. Crea la clase <b>'.$url_rs['controller'].'</b> en el archivo 
						                    <b>'.$url_rs['file_controller'].'</b>  <a class="btn btn-danger" target="_blank"  href="'.fk_link('FkDev/index/?op=createfile&d='.encode('app/controllers').'&f='.encode($url_rs['file_controller']).'&c='.encode($cont_control)).'">Usar Consola Dev 1</a><br> O revisa la bandera <b>fix_path = On</b> en config';
					}else{
						$e->solution='1. Crea la clase <b>'.$url_rs['controller'].'</b> en el archivo 
						                    <b>'.$url_rs['file_controller'].'</b>  <br> O revisa la bandera <b>fix_path = On</b> en config';
					}
										
					$e->solution_code = $cont_control;
					$e->show('code_help');
				}

			}else{
				if(file_exists(SYSTEM_PATH_QRS.'app/Errors/404.php')){
					require(SYSTEM_PATH_QRS.'app/Errors/404.php');
				}else{
					require(dirname(__FILE__).'/Messages/Page/404.php');
				}

			}
		}

	} // End Run
	public static function url_processor($url){
		$file_lst = array();
		if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){
			//----------------
			//MOD REWRITE TRUE
			//----------------
			$url_div = explode('/',$url);
			$tot= count($url_div);

			$cnt = 0;

			for($i=0;$i<$tot;$i++){

				if(trim($url_div[$i])!=''){
					$cnt++;

					$file_lst['url'][$cnt]['value'] = $url_div[$i];
					$file_lst['url'][$cnt]['is_file_or_dir'] = 'dir';
				}

			}
			// last is file

			$file_lst['url'][$cnt]['is_file_or_dir'] = 'file';

		}else{
			//----------------
			//MOD REWRITE FALSE
			//----------------
			$_slash = '/';
			$_q_mark = '?';


			$url_and_vars = explode($_q_mark,$url);

			$the_url = $url_and_vars[0];
			$the_vars = @$url_and_vars[1];

			$url_div = explode($_slash,$the_url);
			$tot= count($url_div);

			$cnt = 0;

			for($i=0;$i<$tot;$i++){

				if(trim($url_div[$i])!=''){
					$cnt++;
					$file_lst['url'][$cnt]['value'] = $url_div[$i];
					$file_lst['url'][$cnt]['is_file_or_dir'] = 'dir';
				}
			}

			// last is file

			$file_lst['url'][$cnt]['is_file_or_dir'] = 'file';

			//get prams
			$the_vars = trim($the_vars,'{');
			$the_vars = trim($the_vars,'}');
			$the_vars_arr = explode(';',$the_vars);

			$file_lst['get_vars']=array();

			if(count($the_vars_arr)>0){
				foreach($the_vars_arr as $k => $v){
					$new_v = explode('=',$v);

					if(isset($new_v[0]) && isset($new_v[1])){
						$file_lst['get_vars'][$new_v[0]]=$new_v[1];
					}
				}
			}
		}

		// return controller
		$controller_name = 'Index';
		$file_controller = $controller_name . 'Controller.php';
		$controller = $controller_name. 'Controller';
		$module = '';
		$action = 'index';

		$i=0;

		// Controller
		if(isset($file_lst['url'][1])){
			$v = $file_lst['url'][1];
			$controller_name = self::camelcase(self::var_format($v['value']));
			$file_controller = $controller_name.'Controller.php';
			$controller = $controller_name.'Controller';
		}

		//Method
		if(isset($file_lst['url'][2])){
			$action = $file_lst['url'][2]['value'];
		}

		$file_rs = array();
		$file_rs['url_processed']= $url;
		$file_rs['module']=self::var_format($module);
		$file_rs['action']=self::var_format($action);

		$file_rs['file_controller']=$file_controller;
		$file_rs['controller']=$controller;
		$file_rs['controller_name']=$controller_name;
		
		//$file_rs['directory_track']=$file_lst['url'];

		$file_rs['get_vars']= $file_lst['get_vars'] ?? null;

		return $file_rs;
	} // url_processor($url)

	public static function fk_autoload($autoload){
		//Section Database
		if($autoload['database']==true){
			Load::database();
		}
		//Section debug
		if($GLOBALS['QRS']['RUNNING']['app']['debug']==true){
			self::autoloader('App/Libraries/Debug',$autoload['quars-debug']);    // debug
		}

		//Section app
		self::autoloader('app/models',$autoload['models']);    // Models
		self::autoloader('app/plugins',$autoload['plugins']);   // Plugins
		self::autoloader('app/libraries',$autoload['libraries']); // Libs
		self::autoloader('app/helpers',$autoload['helpers']);   // Helpers
	}
	private static function autoloader($Dir,$Arr){
		if(count($Arr)>0){
			foreach ($Arr as $k=>$v){
				if($Dir!='app/plugins'){
					Quars::_use($Dir.'/'.$v.'.php');	
				}else{
					Quars::_use('app/plugins/'.$v."/".$v.".class.php");
					Quars::_use("app/plugins/".$v."/".$v.".utils.php");
				}
				
			}
		}
	}

	public static function load_configuration(){
		//--------------------
		// LOAD CONFIG FILES
		//--------------------
		// config.ini
		self::read_config_file('config');
		// database.ini
		self::read_config_file('database');

		// Load constants
		include(SYSTEM_PATH_QRS.'App/Config/app.php');
		
		//--------------------
		// Set view,controler & model files variable
		//--------------------

		//--------------------
		// Set database conection
		//--------------------
		// get app activated
		$app_on = $GLOBALS['QRS']['config']['APP']['app_activated'];
		
		$arr_app_act = $GLOBALS['QRS']['config'][$app_on];
		
		// get environment activated
		$env_on = $arr_app_act['database_mode'];
		
		//--------------------
		// Set HTTP PATH
		//--------------------
		//Set HTTP variable = www_server
		//(moved to from AppController)
		//define('HTTP',$arr_app_act['www_server']);

		// get environment activated variables
		$arr_env = $GLOBALS['QRS']['database'][$env_on];

		$GLOBALS['QRS']['RUNNING']['app'] = $arr_app_act;
		$GLOBALS['QRS']['RUNNING']['db'] = $arr_env;

		// define  database vars
		fk_define('HOST',$arr_env['db_host']);
		fk_define('USER',$arr_env['db_username']);
		fk_define('PASSWORD',$arr_env['db_password']);
		fk_define('SYSTEM_DB',$arr_env['db_name']);
		fk_define('DB_TYPE',$arr_env['db_type']);
		// Inicializar JS links, Css links
		$GLOBALS['QRS']['js_links'] = '';
		$GLOBALS['QRS']['css_links'] = '';

		//SET LANGUAGE
		$DEFAULT_LANGUAGE = $GLOBALS['QRS']['config']['APP']['default_language'];
		$GLOBALS['APP_LANGUAGE']  = $_SESSION['language'] ?? $DEFAULT_LANGUAGE ;

		// autoload: Execute on load 
		include(SYSTEM_PATH_QRS.'App/Config/autoload.php');
	} // read_config

	private static function read_config_file($FILE){
		$cnf = include(SYSTEM_PATH_QRS.'App/Config/'.$FILE.'.php');
		$GLOBALS['QRS'][$FILE] = $cnf;
		/*
		$subsection = false;

		foreach($file_cnf as $k=>$v){

			$v=trim($v);
			$char0=substr($v,0,1);

			if($char0!=';' && $char0!='#' && $v!=''){
				//LINEAS NO COMENTADAS
				$var_value = explode('=',$v);
				$var   = trim($var_value[0]);
				$value = trim(@$var_value[1]);
				$value = trim($value,'"');

				if(strtoupper($value)==="ON"){$value = TRUE;}
				if(strtoupper($value)==="OFF"){$value = FALSE;}




				if($char0=='['){
					// SUB SECTION
					$subsection = true;
					$section_name = trim($var,'[');
					$section_name = trim($section_name,']');
					$section_name = strtoupper($section_name);

				}else{
					// VARS
					if(!$subsection){
						// NO SECCIONES
						$GLOBALS['QRS'][$FILE][$var]=$value;
					}else{
						// SI HAY SECCIONES
						$GLOBALS['QRS'][$FILE][$section_name][$var]=$value;

					}


				}
				//LINEAS NO COMENTADAS
			}

		}
		*/
	} // read_config_file

	/**
	 *@package Quars
	 *@method createUrlRelative()
	 *@desc creates the Url Relative to the Current Url
	 *@example  Current url = "http://example/Controller/Model/var1/"
	 *          relative url = "../../../" , removing Controller/Model/var1/
	 *@since v0.1
	 * */
	public static function createUrlRelative(){

		if(!defined('HTTP')){
				
			// Set the base_url automatically if none was provided
			if ($GLOBALS['QRS']['RUNNING']['app']['www_server'] == '')
			{
				if (isset($_SERVER['HTTP_HOST']))
				{
					$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
					$base_url .= '://'. $_SERVER['HTTP_HOST'];
					$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
				}else{
					$base_url = 'http://localhost/';
				}

				if($GLOBALS['QRS']['config']['APP']['mod_rewrite']){
					$last_folder =  substr($base_url, strlen($base_url) - 7);
					if($last_folder==='public/'){
						$base_url = substr($base_url, 0,-7); // remove public/
					}
				}
				
			}else{
				$base_url = $GLOBALS['QRS']['RUNNING']['app']['www_server'];
			}

			if(defined('GET_PATH_ROOT')){
				$p_root = GET_PATH_ROOT;
				$base_url = preg_replace("/$p_root/", '', $base_url, 1); 
				$base_url = trim($base_url, '/').'/';
			}

			//--------------------
			// Set HTTP PATH
			//--------------------
			//Set HTTP variable = www_server
			define('HTTP',$base_url);			
		}
	} // createUrlRelative

	public static function var_format($txt){
		// Anadimos los guiones
		$find2 = array(' ', '&', '\r\n', '\n', '+');
		$txt = str_replace ($find2, '_', $txt);
		// Eliminamos y Reemplazamos demas caracteres especiales
		//$find3 = array('/[^A-Za-z0-9\-<>.$]/', '/[\-]+/', '/<[^>]*>/');
		$find3 = array('/[^A-Za-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
		$repl = array('_', '_', '');
		$txt = preg_replace ($find3, $repl, $txt);

		return $txt;
	}

	public static function camelcase($s){

		$s = ucwords(strtolower(strtr($s, '_', ' ')));
		$s = str_replace(' ', '', $s);
		return $s;
	}

}
