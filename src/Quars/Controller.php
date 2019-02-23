<?php
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */
 
namespace Quars;

class Controller {

	public $controller = 'index';
	public $action = 'index';
	public $only_ajax = false;
	public $auto_run = false;

	public $templateEngine = 'bladex';

	public $load; // load Object
	public $PermaLinkVars=array();
	public $PermaLinkVarsText = '';

	private $url_processed = ''; // real url used
	
	/**
	 *@package AppController
	 *@method construct()
	 *@desc Creates the controller properties
	 *@since v0.1 beta
	 * */
	public function __construct($url_params=NULL){

		$this->url_processed = isset($url_params['url_processed'])?$url_params['url_processed']:'';
		
		// Define Objeto load
		$this->load = new Load();
		$this->load->RenderEngine = $this->templateEngine;
		
		// Variables modulo y accion
		$this->action = ($url_params['action']!=NULL) ? $url_params['action'] : $this->action;
		$this->controller = substr($url_params['controller'],0,-10); // Set controller name

		// get PermalinkVars
		$this->getPermaLinkVars();

		// ejecuta el method
		if(method_exists($this,$this->action)){
			if($this->auto_run){
				$this->runMetod($this->action);
			}
		}else{
			if($GLOBALS['QRS']['RUNNING']['app']['interactive']==true){

				try{
					if(!method_exists($this,$this->action) ){
						throw new QrsException('La accion "'.$this->action.'" no existe',1);
					}else{
						$this->runMetod($this->action);
					}
				}catch(QrsException $e){

					$e->description = 'El metodo <b>'.$this->action.'</b> de la clase <b>'.get_class($this).'</b>
			                    no fue encontrado';

			         $e->solution    = 'Crea el metodo <b>'.$this->action.'</b> en la clase <b>'.get_class($this).'</b>
			                    en el archivo <b>'.$url_params['file_controller'].'</b> ';           

					if(app_running_is('allow_dev_tools', 1)){

						if(strpos($this->action,'_frm')){
							// frorm
$e->solution_code = '&lt?php
class '.get_class($this).' extends AppController {
	    public function '.$this->action.'() {

        $F = new AppForm("table_name");
        echo $F->render($this->getCurrentUrl());
    
    } // End '.$this->action.'
	
}
';		
						}elseif(strpos($this->action,'_lst')){
							// list
$e->solution_code = '&lt?php
class '.get_class($this).' extends AppController {
	    public function '.$this->action.'() {

      Load::SpecialLib(\'applist.v2\');
      $L = new AppList(\'select * from cotizacion_linea\');
      echo $L->Render($this->getCurrentUrl());
    
    } // End '.$this->action.'
	
}
';		
						}elseif(strpos($this->action,'_spread')){
							// spreadsheet
$e->solution_code = '&lt?php
class '.get_class($this).' extends AppController {
	    public function '.$this->action.'() {

      Load::library(\'AppSpreadSheet.v2\');
      $L = new AppSpreadSheet(\'select * from tabla_name\');
      $L->table_name = \'tabla_name\';
      $L->record_id_name = \'id_tabla_name\';
      $L->formId = \'formid\';
      $L->tableId = \'TBL\';
      // Valores fijos
      $L->fixed_fields [\'id_orden_compra\'] = $id_orden_compra;
      // Fields to save
      $L->fields_to_save[\'cantidad\'] = \'cantidad\';
      $L->useTemplateView(\''.strtolower($this->controller).'/'.str_replace('_spread','.spread',$this->action).'.php\');
      echo $L->Render($this->getCurrentUrl());
    
    } // End '.$this->action.'
	
}
';		

						}else{
							// page
$e->solution_code = '&lt?php
namespace App\Controllers;

use \Quars\Controller;
use \Quars\Bladex;

class '.get_class($this).' extends Controller {
	public function '.$this->action.'() {
		
		$blade = new Bladex();
		// app/Views/'.strtolower($this->controller).'/'.$this->action.'.blade.php 	
		echo $blade->make(\''.strtolower($this->controller).'/'.$this->action.'\', $data);

	} // End '.$this->action.'
}
';		

						}
				
						
					}else{
						$e->solution_code = '&lt?php
						class '.get_class($this).' extends AppController {
							    public function '.$this->action.'() {
    
    } // End '.$this->action.'
	
}
';	
					}

									
												
												 
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


		//ejecuta la vista

		$display_view = true;

		//Validacion de ajax
		if($this->only_ajax == true){
			if(@$_POST['ajax']!=1){
				// No entrar si no es via ajax
				$display_view = false;
			}
		}


	} // End Construct
	
	protected function runMetod($method){
		$this->$method();
	}
	

	/**
	 *@package AppController
	 *@method index()
	 *@desc Default action
	 *@since v0.1 beta
	 * */
	public function index(){
		// default index
	}
	/**
	 *@package AppController
	 *@method getCurrentPage()
	 *@desc Returns the current page location
	 *@example $this->getCurrentPage retuns  MyController/MyAcction
	 *@return string
	 *@since v0.1 beta
	 * */
	private function getCurrentPage(){

		return str_replace('\\App\\Controllers\\', '',$this->controller).'/'.$this->action;
	}

	/**
	 *@package AppController
	 *@method getCurrentUrl()
	 *@desc Returns the Current Url , including permalink vars on it
	 *@example $this->getCurrentUrl retuns  MyController/MyAcction/a/b/c
	 *@return string
	 *@since v0.1
	 * */
	public function getCurrentUrl(){
		/*$get = isset($_GET['url'])?$_GET['url'] : '';
		return $get;*/
		return $this->url_processed;
	}
	/**
	 *@package AppController
	 *@method getPermaLinkVars()
	 *@desc Returns the $_GET variables from a permanent link
	 *@example $this->getPermaLinkVars() from MyController/MyAcction/var1/var2/var3
	 *         retuns  array('var1','var2','var3');
	 *@return array
	 *@since v0.1
	 * */
	public function getPermaLinkVars(){


		$cur_page=$this->getCurrentPage();
		$tot_str = strlen($cur_page);
		
		$url = $this->getCurrentUrl();
		
		
		$vars = substr($url, $tot_str+1);
		$rs_vars = explode('/', $vars);
		$perma_vars = array();
		foreach ($rs_vars as $k => $v) {
			if(trim($v)!=''){
				$perma_vars[]=$v;
			}
		}

		$this->PermaLinkVars = $perma_vars;
		$this->PermaLinkVarsText = $vars;

		return $perma_vars;
	}

	/**
	 *@package AppController
	 *@method page_title()
	 *@desc sets the Html page title
	 *@deprecated use pageTitle() instead
	 *@since v0.1 beta
	 * */
	public function page_title($p){
		$this->pageTitle($p);
	}
	/**
	 *@package AppController
	 *@method pageTitle()
	 *@desc sets the Html page title
	 *@since v0.3.1 beta
	 * */
	public function pageTitle($p){
		# Tittle pagina
		$p = fk_str_format($p,'html');
		fk_page('TITLE',$p);
	} // page_tittle

	/**
	 *@package AppController
	 *@method page_description()
	 *@desc sets the Html meta description
	 *@deprecated use pageDescription() instead
	 *@since v0.1 beta
	 * */
	public function page_description($p){
		$this->pageDescription($p);
	}
	/**
	 *@package AppController
	 *@method pageDescription()
	 *@desc sets the Html meta description
	 *@since v0.3.1 beta
	 * */
	public function pageDescription($p){
		# Descripcion de la pagina
		$p = fk_str_format($p,'html');
		fk_page('DESCRIPTION',$p);
	}	// page_description
	/**
	 *@package AppController
	 *@method page_keywords()
	 *@desc sets the Html meta keywords
	 *@deprecated use pageKeywords() instead
	 *@since v0.1 beta
	 * */
	public function page_keywords($p){
		$this->pageKeywords($p);
	}
	/**
	 *@package AppController
	 *@method pageKeywords()
	 *@desc sets the Html meta keywords
	 *@since v0.3.1 beta
	 * */
	public function pageKeywords($p){
		# Keywords de la pagina
		$p = fk_str_format($p,'html');
		fk_page('KEYWORDS',$p);
	}	// page_KEYWORDSv
	/**
	 *@package AppController
	 *@method menu()
	 *@desc sets the menu selected option
	 *@example Menu('id_menu','selected_option');
	 *@since v0.1 beta
	 * */
	public function menu($m_id,$curr){
		# Keywords de la pagina
		fk_menu($m_id,$curr);
	}	// page_KEYWORDS
	
	/**
	 *@package AppController
	 *@method PutContent()
	 *@desc replaces Content into a view
	 *@example  PutContent('{list}' , $MyListContent );
	 *          will replace the tag {list} with the $MyListContent in the
	 *          $this->Load->View('MyView.tpl') execution
	 *@since v0.1 beta
	 * */
	public function putContent($Index , $Content ){
		$this->load->PutContent($Index , $Content );
	}

	
	/**
	 *@package AppController
	 *@method urlSegment($position);
	 *@desc returns the url parameter of position:   
	 *@example  example: whith http://my-app.com/controller/method/pos1/pos2/pos3/
	 *          urlSegment(0) returns pos1
	 *@since v4.0 
	 * */
	public function urlSegment($position){	
		return (isset($this->PermaLinkVars[$position])? $this->PermaLinkVars[$position]:'');
	}

}