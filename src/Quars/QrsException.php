<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

class QrsException extends \Exception {

	public $description = 'Exception no definida';
	public $solution = 'No hay solucion definida';
	public $solution_code = '';
	public $error_code = '';
	/**
	 *@package QrsException extends Exception
	 *@since v0.1 beta
	 *@method show()
	 *@param $tpl_exception
	 *@desc  Prints the FkExeption error.
	 *       To use the parameter $tpl_exception layouts see the  defined
	 *       templates on Messages/Exceptions
	 * */
	public function show($tpl_exception = 'common'){

		$tpl_exception = dirname(__FILE__) . '/Messages/Exceptions/'.$tpl_exception.'.php';

		// Mensaje amigable en caso que no este activo el interactive
		if(@$GLOBALS['QRS']['RUNNING']['app']['interactive']!=TRUE){
			if(file_exists(SYSTEM_PATH_QRS.'app/errors/error_general.php')){
				$tpl_exception = SYSTEM_PATH_QRS.'app/errors/error_general.php';
			}else{
				$tpl_exception = dirname(__FILE__) . '/Messages/Page/default_error_general.php';
			}
		}

		$inc_files_arr = get_included_files();
		$tot_inc_files = count($inc_files_arr);

		$details = '<h3>Included Files</h3>'.implode('<br />',$inc_files_arr).' <br /> Total:'.$tot_inc_files.
	              '<h3>Memoria Usada</h3><p>'.fk_memory_usage().'</p>';

		$usuario = isset($_SESSION['nombre'])?$_SESSION['nombre']:'';

		$exc_cont = file_get_contents($tpl_exception);
		$find = array('{message}','{trace}','{description}','{solution}','{solution_code}','{details}','{error_code}','<nombreusuario>');
		$repl = array($this->getMessage(),$this,$this->description,$this->solution,$this->solution_code,$details,$this->error_code,$usuario);
		$exc_cont = str_replace($find,$repl,$exc_cont);

		if(php_sapi_name() == "cli"){
			$remote_addr = 'localhost';
		}else{
			$remote_addr = $_SERVER['REMOTE_ADDR'];
		}

		$msg = $this->getMessage();

		if(php_sapi_name() != "cli" && class_exists('Logger')){
			$Log = \Logger::getRootLogger();
			$Log->error($msg);
			$Log->error('IP['.$remote_addr.']');
			$Log->error('Descripcion:'.$this->description.'');
			$Log->error('Detalles:'.$this.'');
		}


		
		// Correo
		if(function_exists("app_running_is")){
			if(app_running_is('on_internet', true)){
				
				$id_usuario = isset($_SESSION['id_usuario'])?$_SESSION['id_usuario']:'N/A';
				$usuario = isset($_SESSION['usuario'])?$_SESSION['usuario']:'N/A';
				$id_cuenta = isset($_SESSION['id_cuenta'])?$_SESSION['id_cuenta']:'N/A';
				$email_usr = isset($_SESSION['email'])?$_SESSION['email']:'N/A';

				$para      = 'miguel.mendoza@totemti.com';
				$titulo = 'Apkube Exception';
				$mensaje = 'UsrID: '.$id_usuario.' '.$usuario.' Cuenta:'.$id_cuenta.' Email: '.$email_usr.',<br>URL['.fk_link().fk_get('url').'] - IP['.$_SERVER['REMOTE_ADDR'].'] -
			'.$this->description.'DESCRIPCION:'.$this->description.' SOLUCION:'.$this->solution.' CODIGO SOLUCION:'.$this->solution_code.$this;
				$cabeceras = 'From: mmendoza@totemti.com' . "\r\n" .
						 'Reply-To: mmendoza@totemti.com' . "\r\n" .
						 'X-Mailer: PHP/' . phpversion();

				mail($para, $titulo, $mensaje, $cabeceras);
			}
		}
		//header('HTTP/1.0 404 Not Found');
		if(fk_post('ajax')==1 || php_sapi_name() == "cli"){
			if(php_sapi_name() == "cli"){
				$exc_cli_cont = $this->getMessage().$this->description;
				echo $exc_cli_cont;
			}else{
				echo $exc_cont;
			}

		}else{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quars</title>
		<?php echo fk_css();?>
		<?php echo fk_js();?>
</head>

<body>
		<?php echo $exc_cont?>
</body>
</html>
		<?php
		}
		die();
	} // QrsException -> show()

}  // End Class
?>
