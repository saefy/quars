<?php
namespace Quars\Libraries;
/**
 * Quars - Framework
 *
 * @package  Quars
 * @author   Miguel Mendoza <mmendoza000@gmail.com>
 */

/**
 * @package    Ajax
 * @desc       Ajax Functions
 * @version    2.0.0
 */

class Ajax{

	public $type = '';
	public $place = '';
	public $params = array();

	/**
	 * @package    Ajax
	 * @desc       Create an Ajax object
	 * @method __construct
	 * @example 1) $fx = new Ajax('form','url:MyController/MyAction;form:myformId');
	 *          2) $fx = new Ajax('url','url:MyController/MyAction');
	 *          3) $fx = new Ajax('url','url:MyController/MyAction;args:x1=val1&x2=val2;url_after:Other/Url;insert_mode:top');
	 *          4) $fx = new Ajax('url','url:MyController/MyAction;url_after:Other/Url;');
	 *          5) $fx = new Ajax('url','url:MyController/MyAction;insert_mode:bottom'); insert mode [bottom|top]
	 *
	 *
	 *
	 *
	 */
	function __construct($type, $place ,$params ){

		$this->type = $type;
		$this->place = $place;
		$p = explode(';',$params);

		foreach($p as $k=>$v){
			if(trim($v)!=''){

				$var_val = explode(':',$v);
				$var = $var_val[0];
				$val = $var_val[1];
				$this->params[$var]=$val;
			}

		}


	} // end __construct()
	/**
	* @package    Ajax
	* @desc       Create an Ajax object
	* @method render()
	* @example 1) OpenJavaScriptTag $fx -> render(); EndJavaScriptTag
	*          2) htmlObject onclic|onmouseout| other javascript event :$fx -> render()
	* */
	public function render(){
		$render_rs = '';

		$showlogin = ',showLoading:true';
		if(isset($this->params['showLoading'])){
			$showlogin = ',showLoading:'.$this->params['showLoading'];
		}

		if($this->type == 'url'){

		 $render_rs = "
		      var pArgs = {pDiv:'".$this->place."', 
						  pUrl:'".$this->params['url']."',
						  pArgs:'".@$this->params['args']."', 
						  pUrlAfter:'".@$this->params['url_after']."', 
						  insertMode:'".@$this->params['insert_mode']."'
						  };
			  fk_ajax_exec(pArgs);
			  ";
		}
		if($this->type == 'submit'){
			$render_rs = "
		      var pArgs = {pDiv:'".$this->place."', 
						  pUrl:'".$this->params['url']."',
						  pForm:'".$this->params['form']."',
						  pArgs:'".@$this->params['args']."',
						  pUrlAfter:'".@$this->params['url_after']."', 
						  insertMode:'".@$this->params['insert_mode']."'
						  ".$showlogin."
						  };
			  fk_ajax_submit(pArgs);
			  ";

		}
		if($this->type == 'json'){
			$render_rs = "
		      var pArgs = {pUrl:'".$this->params['url']."',
						  pArgs:'".@$this->params['args']."'};
			  fk_ajax_json(pArgs,false);
			  ";

		}
		if($this->type == 'json-submit'){
			$render_rs = "var pArgs = {pUrl:'".$this->params['url']."',
		                   pForm:'".$this->params['form']."',
						   pArgs:'".@$this->params['args']."',showLoading:true};
			  fk_ajax_json(pArgs,true);";

		}
		$render_rs = str_replace('
			', '', $render_rs);
		$render_rs = str_replace('  ', '', $render_rs);
		$render_rs = str_replace('	', '', $render_rs);
		return  $render_rs;

	} // end render()

} // End Class
